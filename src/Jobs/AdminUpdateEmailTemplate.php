<?php

namespace AltSolution\Admin\Jobs;

use AltSolution\Admin\EmailTemplate\TemplateRepository;
use AltSolution\Admin\Models\EmailTemplate as TemplateModel;
use App\Jobs\Job;

class AdminUpdateEmailTemplate extends Job
{

    /**
     * Add missed email templates and delete non exists
     *
     * @param TemplateRepository $repository
     * @return void
     */
    public function handle(TemplateRepository $repository)
    {
        $exists = [];

        foreach ($repository->findAll() as $template) {
            $model = TemplateModel::query()
                ->where('name', $template->getName())
                ->first();

            if ($model === null) {
                $model = new TemplateModel();
                $model->name = $template->getName();
                $model->desc = $template->getDescription();
                $model->from = '[%NO.REPLY%]';
                $model->to = '[%ADMIN.EMAIL%]';
                $model->subject = '';
                $model->body = '';
                $model->layout = false;
                $model->html = false;
                $model->to_admin = false;
                $model->to_d_admin = false;

                $model->save();
            }

            $exists[] = $model->id;
        }

        TemplateModel::query()
            ->whereNotIn('id', $exists)
            ->delete();
    }
}
