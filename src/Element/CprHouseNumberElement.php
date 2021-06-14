<?php


namespace Drupal\os2forms_cpr_lookup\Element;

use Drupal\Core\Render\Element\Textfield;

/**
 * @FormElement("cpr_house_number_element")
 */
class CprHouseNumberElement extends Textfield{
  /**
   * {@inheritDoc}
   */
  public static function preRenderTextfield($element) {
    $element = parent::preRenderTextfield($element);
    static::setAttributes($element, ['cpr-house-number']);

    return $element;
  }
}
