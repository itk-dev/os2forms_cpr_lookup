<?php

namespace Drupal\os2forms_cpr_lookup\Element;

use Drupal\Core\Render\Element\Textfield;

/**
 * CPR Address element.
 *
 * @FormElement("cpr_address_element")
 */
class CprAddressElement extends Textfield {

  /**
   * {@inheritDoc}
   */
  public static function preRenderTextfield($element) {
    $element = parent::preRenderTextfield($element);
    static::setAttributes($element, ['cpr-address']);

    return $element;
  }

}
