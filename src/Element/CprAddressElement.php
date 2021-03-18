<?php


namespace Drupal\os2forms_cpr_lookup\Element;

use Drupal\Core\Render\Element\Textfield;

/**
 * @FormElement("cpr_address_element")
 */
class CprAddressElement extends Textfield
{
  public static function preRenderTextfield($element) {
    $element = parent::preRenderTextfield($element);
    static::setAttributes($element, ['cpr-address']);

    return $element;
  }
}
