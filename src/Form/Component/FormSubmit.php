<?php

namespace AltSolution\Admin\Form\Component;

use AltSolution\Admin\Form\AbstractComponent;

class FormSubmit extends AbstractComponent
{
    protected $view = 'form_submit';

    protected function viewData(array $options)
    {
        $data = parent::viewData($options);
        $buttons = $this->getButtons();

        return $data + compact('buttons');
    }

    protected function getButtons()
    {
        $optButtons = $this->getOption('buttons', ['save', 'apply']);
        if (!is_array($optButtons)) {
            throw new \Exception('Buttons must be defined as array');
        }
        $buttons = [];
        foreach ($optButtons as $optBtnKey => $optBtnValue) {
            if (is_array($optBtnValue)) {
                // [[..], [..]]
                $attributes = [];
                $type = array_get($optBtnValue, 'type');
                if ($type !== null) {
                    if (!in_array($type, ['button', 'reset', 'submit'])) {
                        throw new \Exception('Invalid button type: ' . $type);
                    }
                    $attributes['type'] = $type;
                }
                $button = [
                    'icon' => array_get($optBtnValue, 'icon'),
                    'text' => array_get($optBtnValue, 'text'),
                    'attributes' => $attributes,
                ];
                $buttons[] = $button;
            } elseif (is_string($optBtnKey)) {
                // ['fa-icon' => 'Button text']
                $buttons[] = [
                    'icon' => $optBtnKey,
                    'text' => $optBtnValue,
                    'attributes' => [
                        'type' => 'submit',
                    ],
                ];
            } else {
                // predefined buttons
                // ['save', 'apply']
                switch ($optBtnValue) {
                    case 'save':
                        $button = [
                            'icon' => 'fa-save',
                            'text' => trans('admin::common.save'),
                            'attributes' => [
                                'type' => 'submit',
                            ],
                        ];
                        break;
                    case 'apply':
                        $button = [
                            'icon' => 'fa-check',
                            'text' => trans('admin::common.apply'),
                            'attributes' => [
                                'type' => 'submit',
                                'name' => 'button_apply',
                                'value' => 'button_apply',
                            ],
                        ];
                        break;
                    case 'send':
                        $button = [
                            'icon' => 'fa-paper-plane',
                            'text' => trans('admin::common.send'),
                            'attributes' => [
                                'type' => 'submit',
                            ],
                        ];
                        break;
                    case 'exec':
                        $button = [
                            'icon' => 'fa-bolt',
                            'text' => trans('admin::common.exec_btn'),
                            'attributes' => [
                                'type' => 'submit',
                            ],
                        ];
                        break;
                    default:
                        throw new \Exception('Invalid predefined button: ' . $optBtnValue);
                }
                $buttons[] = $button;
            }
        }

        return $buttons;
    }
}
