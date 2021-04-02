<?php

namespace AltSolution\Admin\Forms;

use AltSolution\Admin\Form;

class RoleForm extends Form\AbstractFactory
{
    public function buildForm(Form\BuilderInterface $builder)
    {
        $builder->form([
            'method' => 'post',
            'action' => route('admin::user_roles'),
            'enctype' => 'multipart/form-data',
        ]);

        $builder->hidden('id');
        $builder->text('name', [
            'label' => trans('admin::user.role_name'),
            'help' => trans('admin::user.role_name_description'),
            'required' => true,
        ]);
        $builder->text('description', [
            'label' => trans('admin::user.role_description'),
        ]);
    }
}