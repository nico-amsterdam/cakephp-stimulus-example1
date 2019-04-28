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
      $data += [
         'name'  => '',
         'value' => $data['val'], // or $context->val($data['name']); Example '2018-02-27'
      ];
      return $this->_templates->format('datepicker', [
         'name' => $data['name'],
         'attrs' => $this->_templates->formatAttributes($data, ['name', 'type', 'val'])
      ]);
   }

   public function secureFields(array $data)
   {
      return [$data['name']];
   }
}
