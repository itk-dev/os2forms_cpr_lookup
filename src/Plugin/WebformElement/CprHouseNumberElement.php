<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\os2forms_nemid\Plugin\WebformElement\NemidElementPersonalInterface;

/**
 * @WebformElement(
 *   id = "cpr_house_number_element",
 *   label = "CPR House Number Element",
 *   description = "CPR House Number Element description",
 *   category = "CPR elements"
 * )
 */
class CprHouseNumberElement extends CprLookupElement implements NemidElementPersonalInterface {

  /**
   * {@inheritdoc}
   */
  public function getPrepopulateFieldFieldKey() {
    return 'house_number';
  }

}
