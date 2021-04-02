<?php

namespace AltSolution\Admin\EmailTemplate;

class Template implements TemplateInterface
{
    protected $name = null;
    protected $description = null;
    protected $isMultilingual = false;

    protected $view = null;
    protected $legends = [];

    protected $from = [
        'email' => null,
        'name' => null,
    ];
    protected $to = [
        'email' => null,
        'name' => null
    ];
    //protected $isToAdmin = false;
    protected $subject;
    protected $body;

    public function __construct()
    {
        if (empty($this->name)) {
            throw new Exception('Email template name is empty');
        }

        $this->setDefaultLegend();
        $this->init();

        /*
        if ($result['to']['email'] == '[%ADMIN.EMAIL%]') {
            $this->isToAdmin = true;
        }
        */
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function isMultilingual()
    {
        return $this->isMultilingual;
    }

    public function getView()
    {
        return $this->view;
    }

    public function setNameFrom($name)
    {
        $this->from['name'] = $name;
    }

    protected function setEmailFrom($email)
    {
        $this->from['email'] = $email;
    }

    public function getFrom()
    {
        return $this->from;
    }

    protected function setNameTo($name)
    {
        $this->to['name'] = $name;
    }

    protected function setEmailTo($email)
    {
        $this->to['email'] = $email;
    }

    public function getTo()
    {
        return $this->to;
    }

    protected function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    protected function setLegend(array $legends)
    {
        $this->legends = array_merge($this->legends, $legends);
    }

    public function getLegend()
    {
        return $this->legends;
    }

    protected function init()
    {
        //
    }

    private function setDefaultLegend()
    {
        $legends = [];

        if (config('mail.from.address')) {
            $legends['no.reply'] = trans('admin::email_template.noreply');
        }
        if (cms_option('admin_email')) {
            $legends['admin.email'] = trans('admin::email_template.admin_email');
        }

        $this->setLegend($legends);
    }

}