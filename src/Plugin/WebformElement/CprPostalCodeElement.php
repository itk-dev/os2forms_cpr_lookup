<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\os2forms_nemid\Plugin\WebformElement\NemidElementPersonalInterface;

/**
 * CPR Postal code element.
 *
 * @WebformElement(
 *   id = "cpr_postal_code_element",
 *   label = "CPR Postal Code Element",
 *   description = "CPR Postal Code Element description",
 *   category = "CPR elements"
 * )
 */
class CprPostalCodeElement extends CprLookupElement implements NemidElementPersonalInterface {

  /**
   * {@inheritdoc}
   */
  public function getPrepopulateFieldFieldKey() {
    return 'postal_code';
  }

}
