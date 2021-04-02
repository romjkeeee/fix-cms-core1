<?php

namespace AltSolution\Admin\Forms;

use AltSolution\Admin\Form;
use AltSolution\Admin\Models\AclRole;
use AltSolution\Admin\Modules\User\UserFormInterface;

class UserForm extends Form\AbstractFactory implements UserFormInterface
{
    public function buildForm(Form\BuilderInterface $builder)
    {
        $builder->form([
            'method' => 'post',
            'action' => route('admin::user_save'),
            'enctype' => 'multipart/form-data',
        ]);

        $builder->hidden('id');
        $builder->selectModel('acl_role_id', [
            'label' => trans('admin::user.acl_role'),
            'model' => AclRole::class,
            'title_key' => 'description',
        ]);
        $builder->image('avatar_file', [
            'label' => trans('admin::user.avatar'),
            'help' => trans('admin::user.avatar_description'),
        ]);
        $builder->text('name', [
            'label' => trans('admin::user.username'),
            'required' => true,
        ]);
        $builder->email('email', [
            'label' => trans('admin::user.email'),
            'required' => true,
        ]);
        $builder->password('password', [
            'label' => trans('admin::user.password'),
            'different' => 'name',
            'required' => 'unless:id',
        ]);
        $builder->password('password_confirmation', [
            'label' => trans('admin::user.confirm_password'),
            'identical' => 'password',
            'required' => 'unless:id',
        ]);
        $user = auth()->user();
        $builder->checkbox('active', [
            'label' => trans('admin::user.activate_question'),
            'placeholder' => trans('admin::common.activate'),
            'help' => trans('admin::user.activate_description'),
            'disabled' => 'if:id,' . $user['id'],
        ]);
    }
}