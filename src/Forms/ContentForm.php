<?php

namespace AltSolution\Admin\Forms;

use AltSolution\Admin\Form;
use AltSolution\Admin\Modules\Content\ContentFormInterface;

class ContentForm extends Form\AbstractFactory implements ContentFormInterface
{
    public function buildForm(Form\BuilderInterface $builder)
    {
        $builder->form([
            'method' => 'post',
            'enctype' => 'multipart/form-data',
            'action' => route('admin::content_save'),
        ]);

        $builder->hidden('id');
        $builder->checkbox('active', [
            'label' => trans('admin::content.settings'),
            'placeholder' => trans('admin::content.publish'),
        ]);
        $currentLocale = config('app.locale');
        $builder->slug('url', [
            'source' => 'title_' . $currentLocale,
            'enable' => 'unless:id',
            'label' => trans('admin::content.permalink'),
            'required' => true,
        ]);
        foreach (cms_locales() as $locale) {
            $builder->text('title_' . $locale, [
                'label' => trans('admin::content.name'),
                'required' => $locale == $currentLocale,
            ]);
            $builder->wysiwyg('content_' . $locale, [
                'label' => trans('admin::content.content'),
            ]);
        }
    }
}