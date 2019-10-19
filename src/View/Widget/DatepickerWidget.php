<?php
namespace App\View\Widget;

use Cake\View\Form\ContextInterface;
use Cake\View\Widget\WidgetInterface;

/**
 * HTML5 date. See datepicker template in config/app_form.php
 */
class DatepickerWidget implements WidgetInterface
{
    protected $_templates;

    public function __construct($templates)
    {
        $this->_templates = $templates;
    }

    public function render(array $data, ContextInterface $context)
    {
        $val = $data['val'];
        $date = (($val === null or is_string($val)) ? $val : $val->format('Y-m-d'));
        $data += [
          'name'  => '',
          'value' => $date   // Example: 2018-12-31
        ];
        $excludedAttributeNames = ['name', 'type', 'val'];
        $formattedAttributes = $this->_templates->formatAttributes($data, $excludedAttributeNames);
        return $this->_templates->format('datepicker', [
          'name' => $data['name'],
          'attrs' => $formattedAttributes
        ]);
    }

    public function secureFields(array $data)
    {
        return [$data['name']];
    }
}
