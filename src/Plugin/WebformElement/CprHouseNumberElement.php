<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\os2forms_nemid\Plugin\WebformElement\NemidElementPersonalInterface;

/**
 * CPR House number element.
 *
 * @WebformElement(
 *   id = "cpr_house_number_element",
 *   label = @Translation("CPR House Number Element"),
 *   description = @Translation("CPR House Number Element description"),
 *   category = @Translation("CPR elements")
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
