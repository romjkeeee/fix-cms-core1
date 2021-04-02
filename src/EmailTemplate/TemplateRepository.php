<?php

namespace AltSolution\Admin\EmailTemplate;

class TemplateRepository
{
    /** @var TemplateInterface[] $templates */
    protected $templates;

    public function __construct()
    {
        $this->templates = app()->tagged('admin.email-template');
    }

    function findAll()
    {
        return $this->templates;
    }

    function findByName($name)
    {
        foreach ($this->templates as $template) {
            if ($template->getName() == studly_case($name)) {
                return $template;
            }
        }

        return null;
    }
}
