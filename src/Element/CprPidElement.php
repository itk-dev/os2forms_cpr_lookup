<?php

namespace Drupal\os2forms_cpr_lookup\Element;

use Drupal\Core\Render\Element\Textfield;

/**
 * CPR Pid element.
 *
 * @FormElement("cpr_pid_element")
 */
class CprPidElement extends Textfield {

  /**
   * {@inheritDoc}
   */
  public static function preRenderTextfield($element) {
    $element = parent::preRenderTextfield($element);
    static::setAttributes($element, ['cpr-pid']);

    return $element;
  }

}
