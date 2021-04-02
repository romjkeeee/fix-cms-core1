<?php

namespace AltSolution\Admin\EmailTemplate;

use Psr\Log\LoggerAwareTrait;
use Illuminate\Mail\Mailer as RealMailer;
use Illuminate\Mail\Message;
use AltSolution\Admin\Models\EmailTemplate as TemplateModel;

class Mailer implements MailerInterface
{
    use LoggerAwareTrait;

    public function send(TemplateInterface $template, array $data = null)
    {
        $model = $this->getTemplateModel($template->getName());
        $props = $this->joinTemplate($template, $model);
        $legends = $template->getLegend();
        $props = $this->parseTemplate($props, $legends, $data);

        $view = $template->getView();
        if (!$model['layout'] || !$view) {
            $view = 'admin::email_template.email_body';
        }
        if (!$model['html']) {
            $props['body'] = nl2br($props['body']);
        }

        $sent = 0;

        if ($props['to']['email']) {
            $cc = $this->carbonCopy($model);

            $twig_env = $this->bootTwig();
            $body = $twig_env->render($props['body']);

            /** @var RealMailer $mailer */
            $mailer = app(RealMailer::class);
            try {
                /** @noinspection PhpVoidFunctionResultUsedInspection */
                $sent = $mailer->send($view, compact('body'), function (Message $m) use ($props, $cc, $data) {
                    $m->from($props['from']['email'], $props['from']['name']);
                    $m->to($props['to']['email'], $props['to']['name']);
                    foreach ($cc as $c) {
                        $m->bcc($c['email'], $c['name']);
                    }
                    $m->subject($props['subject']);

                    if (isset($data['attaches'])) {
                        $this->addAttach($m, $data['attaches']);
                    }
                });

                $this->logger->info('Sent email', [
                    'to' => $props['to'],
                    'subject' => $props['subject'],
                    'sent' => $sent,
                ]);

            } catch (\Exception $e) {
                $this->logger->error('Failed sent email', [$e]);
            }
        }

        return $sent;
    }

    /**
     * @param Message $message
     * @param mixed $attach
     */
    private function addAttach(Message $message, $attach)
    {
        if (is_array($attach) && array_key_exists('path', $attach)) {
            if ($attach['path']) {
                $as = isset($attach['as']) && is_string($attach['as']) ? $attach['as'] : null;
                $mime = isset($attach['mime']) && is_string($attach['mime']) ? $attach['mime'] : null;
                $message->attach($attach['path'], ['as' => $as, 'mime' => $mime]);
            }
        } elseif (is_array($attach)) {
            foreach ($attach as $a) {
                if (isset($a['path']) && $a['path']) {
                    $as = isset($a['as']) && is_string($a['as']) ? $a['as'] : null;
                    $mime = isset($a['mime']) && is_string($a['mime']) ? $a['mime'] : null;
                    $message->attach($a['path'], ['as' => $as, 'mime' => $mime]);
                }
            }
        } else {
            if ($attach && is_string($attach)) {
                $message->attach($attach);
            }
        }
    }

    private function getDefaultData()
    {
        $data = [];

        if ($noReply = config('mail.from.address')) {
            $data['no.reply'] = $noReply;
        }
        if ($adminEmail = cms_option('admin_email')) {
            $data['admin.email'] = $adminEmail;
        }

        return $data;
    }

    private function joinTemplate(TemplateInterface $template, TemplateModel $model)
    {
        $props = [
            'from' => $template->getFrom(),
            'to' => $template->getTo(),
            'subject' => $template->getSubject(),
            'body' => null,
        ];

        $props['to']['email'] = $model['to'];
        $props['from']['email'] = $model['from'];
        if ($template->isMultilingual()) {
            $props['subject'] = $model->trans('subject');
            $props['body'] = $model->trans('body');
        } else {
            $props['subject'] = $model['subject'];
            $props['body'] = $model['body'];
        }

        return $props;
    }

    private function parseTemplate(array $props, array $legends, array $data = null)
    {
        if (is_array($data)) {
            $data = array_merge($this->getDefaultData(), $data);
        } else {
            $data = $this->getDefaultData();
        }

        $replacePairs = [];
        foreach ($legends as $legendName => $_) {
            $key = '[%' . strtoupper($legendName) . '%]';
            $value = array_get($data, $legendName);
            $replacePairs[$key] = $value;
        }

        $props['to']['email'] = strtr($props['to']['email'], $replacePairs);
        $props['to']['name'] = strtr($props['to']['name'], $replacePairs);
        $props['from']['email'] = strtr($props['from']['email'], $replacePairs);
        $props['from']['name'] = strtr($props['from']['name'], $replacePairs);
        $props['subject'] = strtr($props['subject'], $replacePairs);
        $props['body'] = strtr($props['body'], $replacePairs);

        return $props;
    }

    /**
     * @param string $name
     * @return TemplateModel
     * @throws Exception
     */
    private function getTemplateModel($name)
    {
        /** @var TemplateModel|null $model */
        $model = TemplateModel::query()
            ->where('name', $name)
            ->first();

        if ($model === null) {
            throw new Exception('Template model not loaded');
        }

        return $model;
    }

    private function carbonCopy(TemplateModel $model)
    {
        $cc = [];

        $trimAndValidate = function ($email) {
            $email = trim($email);
            $email = filter_var($email, FILTER_VALIDATE_EMAIL);

            return $email;
        };

        if ($model['to_d_admin']) {
            $duplicateEmails = explode(',', cms_option('admin_email_d'));
            $duplicateEmails = array_map($trimAndValidate, $duplicateEmails);
            $duplicateEmails = array_filter($duplicateEmails);

            foreach ($duplicateEmails as $duplicateEmail) {
                $cc[] = [
                    'email' => $duplicateEmail,
                    'name' => null,
                ];
            }
        }

        if ($model['to_admin']) {
            $duplicateEmails = [cms_option('admin_email')];
            $duplicateEmails = array_map($trimAndValidate, $duplicateEmails);
            $duplicateEmails = array_filter($duplicateEmails);

            foreach ($duplicateEmails as $duplicateEmail) {
                $cc[] = [
                    'email' => $duplicateEmail,
                    'name' => null,
                ];
            }
        }

        return $cc;
    }

    /**
     * @return \Twig_Environment
     * @throws \Exception
     */
    private function bootTwig()
    {
        $twig_env = new \Twig_Environment(new \Twig_Loader_String);
        $extensions = config('twigbridge.extensions.enabled');
        foreach ($extensions as $extension) {
            if (is_string($extension)) {
                try {
                    $extension = app($extension);
                } catch (\Exception $e) {
                    throw new \Exception("Cannot instantiate Twig extension '$extension': " . $e->getMessage());
                }
            } elseif (is_callable($extension)) {
                $extension = $extension(app(), $twig_env);
            } elseif (!is_a($extension, 'Twig_Extension')) {
                throw new \InvalidArgumentException('Incorrect extension type');
            }
            $twig_env->addExtension($extension);
        }

        return $twig_env;
    }
}
