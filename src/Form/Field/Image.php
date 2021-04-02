<?php

namespace AltSolution\Admin\Form\Field;

use AltSolution\Admin\Form\AbstractField;
use AltSolution\Admin\Helpers\ImagesInterface;

class Image extends AbstractField
{
    protected $view = 'image';
    protected $rules = ['file'];

    protected function viewData(array $options)
    {
        $data = parent::viewData($options);
        $empty = array_get($data['options'], 'empty', true);
        $empty = $this->normalizeBoolean($empty);

        $image = null;
        if ($this->value) {
            $size = array_get($data, 'options.image_size', 'list');
            $dataSource = $this->getForm()->getData();
            if (strpos($this->name, '.') === false) {
                if (!$dataSource instanceof ImagesInterface) {
                    throw new \Exception('Data source not implements ImagesInterface');
                }
                $image = $dataSource->imageUrl($this->name, $size);
            } else {
                $pos = strrpos($this->name, '.');
                $ns = substr($this->name, 0, $pos);
                $name = substr($this->name, $pos + 1);

                $ds = array_get($dataSource, $ns);
                if (!$ds instanceof ImagesInterface) {
                    throw new \Exception('Data source not implements ImagesInterface');
                }
                $image = $ds->imageUrl($name, $size);
            }

            $data['name_delete'] = $this->transformName($this->name . '_delete');
        }

        return $data + compact('image', 'empty');
    }

}