<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\os2forms_nemid\Plugin\WebformElement\NemidElementPersonalInterface;

/**
 * CPR Postal code element.
 *
 * @WebformElement(
 *   id = "cpr_postal_code_element",
 *   label = @Translation("CPR Postal Code Element"),
 *   description = @Translation("CPR Postal Code Element description"),
 *   category = @Translation("CPR elements")
 * )
 */
class CprPostalCodeElement extends CprLookupElement implements NemidElementPersonalInterface {

  /**
   * {@inheritdoc}
   */
  public function getPrepopulateFieldFieldKey(array &$element) {
    return 'postal_code';
  }

}
