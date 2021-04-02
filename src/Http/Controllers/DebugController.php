<?php

namespace AltSolution\Admin\Http\Controllers;

use Illuminate\Database\DatabaseManager as DBM;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;

class DebugController extends Controller
{
    public function emailTest()
    {
        $this->authorize('permission', 'debug');

        $form = cms_construct_form(function() {
            yield [
                'type' => 'form',
                'method' => 'post',
                'buttons' => ['send'],
            ];
            yield 'email' => [
                'type' => 'email',
                'label' => trans('admin::debug.test_email'),
                'help' => trans('admin::debug.email_test_description'),
                'required' => true,
            ];
        });

        $this->layout
            ->setActiveSection('debug')
            ->setTitle(trans('admin::debug.email_test_title'));

        return view('admin::debug.email_test', compact('form'));
    }

    public function emailTestPost(Request $request)
    {
        $this->authorize('permission', 'debug');

        $this->validate($request, [
            'email' => 'required|email',
        ]);

        $fromEmail = config('mail.from.address');
        $toEmail = $request['email'];
        $body = trans('admin::debug.email_test_text');

        $errors = [];
        $mailer = app(Mailer::class);
        try {
            $mailer->raw($body, function (Message $m) use ($fromEmail, $toEmail) {
                $m->from($fromEmail, trans('admin::debug.email_test_name'));
                $m->to($toEmail, trans('admin::debug.email_test_toName'));
                $m->subject(trans('admin::debug.email_test_subject'));
            });
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        if (count($mailer->failures())) {
            foreach ($mailer->failures as $failure) {
                $errors[] = $failure;
            }
        }
        if (count($errors)) {
            return redirect()->route('admin::email_test')->withErrors($errors)->withInput();
        }

        $this->layout->addNotify('success', trans('admin::debug.email_test_sent'));
        return redirect()->route('admin::email_test')->withInput();
    }

    public function php(Request $request)
    {
        $this->authorize('permission', 'debug');

        $code = trim($request['code']);
        $res = '';
        $errors = [];
        if ($code) {
            $errorHandler = function ($errno, $errstr, $errfile, $errline) {
                echo "{$errstr} on {$errfile}:{$errline}" . PHP_EOL;
                return true;
            };
            ob_start();
            $lastErrorHandler = set_error_handler($errorHandler);
            $ret = eval($code);
            echo "\n => ";
            var_dump($ret);
            set_error_handler($lastErrorHandler);
            $res = ob_get_clean();
        }

        $form = cms_construct_form(function() {
            yield [
                'type' => 'form',
                'method' => 'post',
                'buttons' => ['exec']
            ];
            yield 'code' => [
                'type' => 'textarea',
                'label' => trans('admin::debug.f_code'),
            ];
            yield 'res' => [
                'type' => 'textarea',
                'rows' => 15,
                'label' => trans('admin::debug.f_result'),
                'readonly' => true,
            ];
        });
        $form->setDataSource(compact('code', 'res'));

        $this->layout
            ->setActiveSection('debug')
            ->setTitle(trans('admin::debug.php_title'));

        return view('admin::debug.php', compact('form'))
            ->withErrors($errors);
    }

    public function phpinfo(Request $request)
    {
        $this->authorize('permission', 'debug');

        $getInfo = function($what = -1) {
            ob_start();
            phpinfo($what);
            return ob_get_clean();

        };

        if ($request['raw'] || !extension_loaded('dom')) {
            return $getInfo();
        }

        $parseInfo = function($htmlStr) {
            $html = new \DOMDocument('1.0', 'utf-8');
            $html->loadHTML($htmlStr);

            $body = $html->getElementsByTagName("body")->item(0);

            $result = '';
            foreach ($body->childNodes as $childNode) {
                $result .= $html->saveHTML($childNode);
            }

            return $result;
        };

        $info = [
            // Строка конфигурации, расположение php.ini, дата сборки, Web-сервер, Система и др.
            'general' => $parseInfo($getInfo(INFO_GENERAL)),
            // Разработчики PHP. См. также phpcredits().
            'credits' => $parseInfo($getInfo(INFO_CREDITS)),
            // Текущие значение основных и локальных PHP директив. См. также ini_get().
            'configuration' => $parseInfo($getInfo(INFO_CONFIGURATION)),
            // 	Загруженные модули и их настройки. См. также get_loaded_extensions().
            'modules' => $parseInfo($getInfo(INFO_MODULES)),
            // 	Информация о переменных окружения, которая также доступна в $_ENV.
            'environment' => $parseInfo($getInfo(INFO_ENVIRONMENT)),
            // Выводит все предопределенные переменные из EGPCS (Environment, GET, POST, Cookie, Server).
            'variables' => $parseInfo($getInfo(INFO_VARIABLES)),
            // Информация о лицензии PHP. См. также » license FAQ.
            'license' => $parseInfo($getInfo(INFO_LICENSE)),
        ];

        $this->layout
            ->setActiveSection('debug')
            ->setTitle(trans('admin::debug.phpinfo_title'));

        return view('admin::debug.phpinfo', compact('info'));
    }

    public function sql(Request $request, DBM $databaseManager)
    {
        $this->authorize('permission', 'debug');

        $code = trim($request['code']);
        $result = [];
        if ($code) {
            $lines = preg_split('~[\r\n]+~', $code);
            $commands = '';
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line && !preg_match('~^--~', $line)) {
                    $commands .= $line;
                }
            }
            $commands = array_filter(explode(';', $commands));

            $conn = $databaseManager->connection('mysql');

            foreach ($commands as $cmd) {
                $msg = [];
                $head = [];
                $body = [];
                $msg[] = 'QUERY: ' . $cmd;
                $type = strtolower(substr($cmd, 0, 4));
                switch ($type) {
                    case 'sele':
                    case 'show':
                    case 'expl':
                    case 'help':
                        try {
                            $res = $conn->select($conn->raw($cmd));
                            if (!count($res)) {
                                $msg[] = 'WARNING: no results found';
                                break;
                            }

                            foreach ($res as $r) {
                                foreach ($r as $key => $_) {
                                    $head[] = $key;
                                }
                                break;
                            }
                            foreach ($res as $r) {
                                $row = [];
                                foreach ($r as $value) {
                                    $row[] = $value;
                                }
                                $body[] = $row;
                            }
                        } catch (\Exception $de) {
                            $msg[] = 'ERROR: ' . $de->getMessage();
                        }
                        break;
                    default:
                        try {
                            $res = $conn->affectingStatement($cmd);
                            $msg[] = sprintf('%d rows affected', $res);
                        } catch (\Exception $de) {
                            $msg[] = 'ERROR: ' . $de->getMessage();
                        }
                        break;
                }
                $result[] = compact('msg', 'head', 'body');
            }
        }

        $form = cms_construct_form(function() {
            yield [
                'type' => 'form',
                'method' => 'post',
                'buttons' => ['exec']
            ];
            yield 'code' => [
                'type' => 'textarea',
                'label' => trans('admin::debug.f_code'),
            ];
        });
        $form->setDataSource(compact('code'));

        $this->layout
            ->setActiveSection('debug')
            ->setTitle(trans('admin::debug.sql_title'));

        return view('admin::debug.sql', compact('form', 'result'));
    }

    public function artisan()
    {
        $this->layout
            ->setActiveSection('debug')
            ->setTitle(trans('admin::debug.artisan_title'));
        return view('admin::debug.artisan');
    }

    public function artisanRpc(Request $request)
    {
        //$options = json_decode($request->getContent());
        //$command = implode(' ', $options->params);
        $command = $request->input('cmd');
        try {
            \Artisan::call($command);
            $output = \Artisan::output();
        } catch (\Exception $e) {
            $output = $e->getMessage();
        }

        return compact('output');
        //return [0, $output];

        // list($status, $output) = $this->runCommand();

        /*
        $cmd = base_path("artisan $command 2>&1");
        $handler = popen($cmd, 'r');
        $output = '';
        while (!feof($handler)) {
            $output .= fgets($handler);
        }
        $output = trim($output);
        $status = pclose($handler);
        return [$status, $output];
         */

    }
}
