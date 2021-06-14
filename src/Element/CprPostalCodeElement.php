<?php


namespace Drupal\os2forms_cpr_lookup\Element;

use Drupal\Core\Render\Element\Textfield;

/**
 * @FormElement("cpr_postal_code_element")
 */
class CprPostalCodeElement extends Textfield {
  /**
   * {@inheritDoc}
   */
  public static function preRenderTextfield($element) {
    $element = parent::preRenderTextfield($element);
    static::setAttributes($element, ['cpr-postal-code']);

    return $element;
  }
}
