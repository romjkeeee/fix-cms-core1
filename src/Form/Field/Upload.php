<?php

namespace AltSolution\Admin\Form\Field;

use AltSolution\Admin\Form\AbstractField;
use AltSolution\Admin\Helpers\UploadsInterface;

class Upload extends AbstractField
{
    protected $view = 'upload';
    protected $rules = ['file'];

    protected function viewData(array $options)
    {
        $data = parent::viewData($options);
        $empty = array_get($data['options'], 'empty', true);
        $empty = $this->normalizeBoolean($empty);

        $file = null;
        if ($this->value) {
            $dataSource = $this->getForm()->getData();
            if (strpos($this->name, '.') === false) {
                if (!$dataSource instanceof UploadsInterface) {
                    throw new \Exception('Data source not implements UploadsInterface');
                }
                $file = $dataSource->uploadUrl($this->name);
            } else {
                $pos = strrpos($this->name, '.');
                $ns = substr($this->name, 0, $pos);
                $name = substr($this->name, $pos + 1);

                $ds = array_get($dataSource, $ns);
                if (!$ds instanceof UploadsInterface) {
                    throw new \Exception('Data source not implements UploadsInterface');
                }
                $file = $ds->uploadUrl($name);
            }

            $data['name_delete'] = $this->transformName($this->name . '_delete');
        }

        return $data + compact('file', 'empty');
    }

}