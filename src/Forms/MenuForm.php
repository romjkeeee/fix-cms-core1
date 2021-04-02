<?php

namespace AltSolution\Admin\Forms;

use AltSolution\Admin\Form;

class MenuForm extends Form\AbstractFactory
{
    public function buildForm(Form\BuilderInterface $builder)
    {
        $builder->form([
            'method' => 'post',
            'action' => route('admin::menu_save'),
        ]);

        $builder->hidden('id');
        $builder->text('name', [
            'label' => trans('admin::menu.name'),
            'help' => trans('admin::menu.name_help'),
            'required' => true,
        ]);
        $builder->text('description', [
            'label' => trans('admin::menu.title_menu'),
            'required' => true,
        ]);
    }
}