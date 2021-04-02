<?php

namespace AltSolution\Admin\Http\Controllers;

use AltSolution\Admin\EmailTemplate\TemplateRepository;
use AltSolution\Admin\Forms\EmailTemplateForm;
use AltSolution\Admin\Jobs\AdminUpdateEmailTemplate;
use Illuminate\Http\Request;
use AltSolution\Admin\Models\EmailTemplate;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $this->authorize('permission', 'email_template.list');

        $this->dispatch(new AdminUpdateEmailTemplate());

        $emailTemplates = EmailTemplate::query()
            ->paginate(config('admin.item_per_page', 20));

        $this->layout
            ->setActiveSection('email_template')
            ->setTitle(trans('admin::email_template.title'));
        return view('admin::email_template.list', compact('emailTemplates'));
    }

    public function edit($id = null)
    {
        $this->authorize('permission', 'email_template.edit');

        $emailTemplate = EmailTemplate::query()
            ->findOrFail($id);

        $name = $emailTemplate['name'];
        $template = app(TemplateRepository::class)->findByName($name);
        if (is_null($template)) {
            abort(503, 'Template not found: ' . $name);
        }

        $form = cms_create_form(EmailTemplateForm::class, $emailTemplate);

        $this->layout
            ->setActiveSection('email_template')
            ->setTitle(trans($emailTemplate ? 'admin::email_template.edit' : 'admin::email_template.add'))
            ->addBreadcrumb(trans('admin::email_template.title'), route('admin::email_template_list'));
        return view('admin::email_template.edit', compact('form', 'template'));
    }

    public function save(Request $request)
    {
        $this->authorize('permission', 'email_template.edit');

        $name = $request['name'];
        $template = app(TemplateRepository::class)->findByName($name);
        if (is_null($template)) {
            abort(503, 'Template not found: ' . $name);
        }

        $rules = [
            'name' => 'required',
            'desc' => 'required',
            'from' => 'required',
            'to' => 'required',
        ];
        if ($template->isMultilingual()) {
            $locale = config('app.locale');
            $rules += [
                'subject_' . $locale => 'required',
            ];
        } else {
            $rules += [
                'subject' => 'required'
            ];
        }
        $this->validate($request, $rules);

        $model = EmailTemplate::query()->firstOrNew(['id' => $request->id]);
        $model->fill($request->all());
        $model->layout = (bool)$request->layout;
        $model->html = (bool)$request->html;
        $model->to_admin = (bool)$request->to_admin;
        $model->to_d_admin = (bool)$request->to_d_admin;
        $model->modified = time();
        $model->save();

        $this->layout->addNotify('success', trans('admin::email_template.saved'));

        if ($request['button_apply']) {
            return redirect(route('admin::email_template_edit', ['id' => $model->id]));
        }

        return redirect(route('admin::email_template_list'));
    }
}
