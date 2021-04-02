<?php

namespace AltSolution\Admin\Forms;

use AltSolution\Admin\Form;

class MenuItemForm extends Form\AbstractFactory
{
    public function buildForm(Form\BuilderInterface $builder)
    {
        $builder->form([
            'method' => 'post',
            'action' => url('/admin/menu/saveitem'),
        ]);

        $builder->hidden('id');
        $builder->hidden('menu_id');

        $currentLocale = config('app.locale');
        foreach (config('app.locales') as $locale => $localeTitle) {
            $builder->text('name_' . $locale, [
                'label' => trans('admin::menu.name_item') . sprintf(' (%s)', $localeTitle),
                'required' => $locale == $currentLocale,
            ]);
        }
        $builder->select('parent_id', [
            'label' => trans('admin::menu.name_parent_id'),
            'choices' => function() use ($builder) {
                yield 0 => trans('admin::menu.root');
                $parent_items = $this->provided('parent_items');
                foreach ($parent_items as $item) {
                    yield $item->id => $item->trans('name');
                }
            }
        ]);
        $builder->text('url', [
            'label' => trans('admin::menu.url_item'),
            'readonly' => true,
        ]);
    }
}