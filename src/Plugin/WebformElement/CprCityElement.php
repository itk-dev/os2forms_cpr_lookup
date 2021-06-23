<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\os2forms_nemid\Plugin\WebformElement\NemidElementPersonalInterface;

/**
 * CPR City element.
 *
 * @WebformElement(
 *   id = "cpr_city_element",
 *   label = "CPR City Element",
 *   description = "CPR City Element description",
 *   category = "CPR elements"
 * )
 */
class CprCityElement extends CprLookupElement implements NemidElementPersonalInterface {

  /**
   * {@inheritdoc}
   */
  public function getPrepopulateFieldFieldKey() {
    return 'city';
  }

}
