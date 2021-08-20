<?php

namespace Drupal\os2forms_cpr_lookup\Element;

use Drupal\Core\Render\Element\Textfield;

/**
 * CPR Value Element.
 *
 * @FormElement("cpr_value_element")
 */
class CprValueElement extends Textfield {

  /**
   * {@inheritDoc}
   */
  public static function preRenderTextfield($element) {
    $element = parent::preRenderTextfield($element);
    static::setAttributes($element, ['cpr-value']);

    return $element;
  }

}
