<?php

namespace AltSolution\Admin\Forms;

use AltSolution\Admin\Form;

class EmailTemplateForm extends Form\AbstractFactory
{
    public function buildForm(Form\BuilderInterface $builder)
    {
        $builder->form([
            'method' => 'post',
            'action' => route('admin::email_template_save'),
        ]);

        $builder->hidden('id');
        $builder->text('name', [
            'label' => trans('admin::email_template.name'),
            'help' => trans('admin::email_template.name_help'),
            'required' => true,
            'readonly' => true,
        ]);
        $builder->text('desc', [
            'label' => trans('admin::email_template.desc'),
            'required' => true,
        ]);
        $builder->text('from', [
            'label' => trans('admin::email_template.from'),
            'required' => true,
        ]);
        $builder->text('to', [
            'label' => trans('admin::email_template.to'),
            'required' => true,
        ]);
        $builder->checkbox('to_admin', [
            'label' => trans('admin::email_template.to_admin'),
            'placeholder' => trans('admin::common.activate'),
            'help' => trans('admin::email_template.to_admin_description'),
        ]);
        $builder->checkbox('to_d_admin', [
            'label' => trans('admin::email_template.to_d_admin'),
            'placeholder' => trans('admin::common.activate'),
            'help' => trans('admin::email_template.to_d_admin_description'),
        ]);
        $builder->text('subject', [
            'label' => trans('admin::email_template.subject'),
            'required' => true,
        ]);
        $builder->textarea('body', [
            'label' => trans('admin::email_template.body'),
            'rows' => 7,
        ]);
        $builder->checkbox('html', [
            'label' => trans('admin::email_template.html'),
            'placeholder' => trans('admin::email_template.publish'),
        ]);
        $builder->checkbox('layout', [
            'label' => trans('admin::email_template.layout'),
            'placeholder' => trans('admin::email_template.publish'),
        ]);
        $currentLocale = config('app.locale');
        foreach (cms_locales() as $locale) {
            $builder->text('subject_' . $locale, [
                'label' => trans('admin::email_template.subject'),
                'required' => $locale == $currentLocale,
            ]);
            $builder->textarea('body_' . $locale, [
                'label' => trans('admin::email_template.body'),
                'rows' => 7,
            ]);
        }
    }
}