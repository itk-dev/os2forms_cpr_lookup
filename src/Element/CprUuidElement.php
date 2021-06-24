<?php

namespace Drupal\os2forms_cpr_lookup\Element;

use Drupal\Core\Render\Element\Textfield;

/**
 * CPR Uuid element.
 *
 * @see https://migrering.nemlog-in.dk/nemlog-in-broker/privat-tjenesteudbyder/opslagstjenester/erstatning-til-pid-rid-uuid/
 *
 * @FormElement("cpr_uuid_element")
 */
class CprUuidElement extends Textfield {

  /**
   * {@inheritDoc}
   */
  public static function preRenderTextfield($element) {
    $element = parent::preRenderTextfield($element);
    static::setAttributes($element, ['cpr-uuid']);

    return $element;
  }

}
