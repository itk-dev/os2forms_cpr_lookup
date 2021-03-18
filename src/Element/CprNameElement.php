<?php


namespace Drupal\os2forms_cpr_lookup\Element;


use Drupal\Core\Render\Annotation\FormElement;
use Drupal\Core\Render\Element\Textfield;

/**
 * @FormElement("cpr_name_element")
 */
class CprNameElement extends Textfield
{
  public static function preRenderTextfield($element) {
    $element = parent::preRenderTextfield($element);
    static::setAttributes($element, ['cpr-name']);

    return $element;
  }
}
