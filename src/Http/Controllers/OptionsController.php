<?php

namespace AltSolution\Admin\Http\Controllers;

use AltSolution\Admin\Option;
use AltSolution\Admin\Seo\SeoRepositoryInterface;
use Illuminate\Http\Request;

class OptionsController extends Controller
{
    public function getIndex()
    {
        $this->authorize('permission', 'options');

        $values = app(Option\RepositoryInterface::class)->getAll();
        $forms = [];
        $option = cms_system()->getOption();
        foreach ($option->getSections() as $section) {
            $forms[$section->getName()] = $section->getFormBuilder()
                ->setPrefix($section->getName() . '_')
                ->setDataSource($values)
                ->build();
        }
        $form = cms_construct_form(function() {
            yield [
                'type' => 'form',
                'method' => 'post',
                'buttons' => ['save'],
            ];
        });

        $this->layout
            ->setActiveSection('options')
            ->setTitle(trans('admin::option.title'));
    	return view('admin::options.option', compact('option', 'form', 'forms'));
    }

    public function postIndex(Request $request)
    {
        $this->authorize('permission', 'options');

        $option = cms_system()->getOption();
        foreach ($option->getSections() as $section) {
            $form = $section->getFormBuilder()
                ->setPrefix($section->getName() . '_')
                ->build();

            $options = [];
            foreach ($form->getFields() as $field) {
                $fieldName = $field->getName();
                $options[$fieldName] = $request[$fieldName];
            }

            // todo: not cool
            $this->getValidationFactory()
                ->extend('email_many', function ($attribute, $value, $parameters, $validator) {
                    $valueArr = explode(',', $value);
                    $valueArr = array_map('trim', $valueArr);
                    foreach ($valueArr as $valueOne) {
                        if (filter_var($valueOne, FILTER_VALIDATE_EMAIL) === false) {
                            $validator->errors()->add($attribute, trans('validation.email', compact('attribute')));
                            break;
                        }
                    }
                    return true;
                });
            $rules = $form->getValidationRules();
            $rules['admin_email_d'] = ['email_many'];
            $validator = $this->getValidationFactory()
                ->make($options, $rules, [], $form->getValidationAttributes());
            if ($validator->fails()) {
                return redirect()->route('admin::options')
                    ->withErrors($validator)
                    ->withInput();
            }

            app(Option\RepositoryInterface::class)->setMany($options);
        }

        $this->layout->addNotify('success', trans('admin::option.saved'));

        return redirect()->route('admin::options');
    }

    public function getSeo()
    {
        $this->authorize('permission', 'options');

        // TODO: move from controller
        $locales = cms_locales();
        $seo = cms_system()->getSeo();
        $values = app(SeoRepositoryInterface::class)->getPagesProperties();

        $forms = [];
        foreach ($seo->getSections() as $section) {
            $forms[$section->getName()] = [];
            foreach ($locales as $locale) {
                $form = cms_construct_form(function () use ($locale, $section) {
                    foreach ($section->getFields() as $field) {
                        $fieldName = sprintf('seo.%s.%s.%s', $section->getName(), $locale, $field->getName());
                        yield $fieldName => [
                            'type' => $field->getType(),
                            'label' => trans((string)$field->getDescription()),
                            'help' => trans((string)$field->getHelp()),
                        ];
                    }
                });
                $form->setDataSource([
                    'seo' => [
                        $section->getName() => array_get($values, $section->getName()),
                    ],
                ]);
                $forms[$section->getName()][$locale] = $form;
            }
        }

        $this->layout
            ->setActiveSection('options')
            ->setTitle(trans('admin::option.seo_title'));
        return view('admin::options.seo', compact('seo', 'locales', 'forms'));
    }

    public function postSeo(Request $request)
    {
        $this->authorize('permission', 'options');

        $seo = $request->input('seo');
        // TODO: move checks
        app(SeoRepositoryInterface::class)->setPagesProperties($seo);

        $this->layout->addNotify('success', trans('admin::option.seo_saved'));

        return redirect()->route('admin::seo');
    }
}