<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\os2forms_nemid\Plugin\WebformElement\NemidElementPersonalInterface;

/**
 * @WebformElement(
 *   id = "cpr_address_element",
 *   label = "CPR Address Element",
 *   description = "CPR Address Element description",
 *   category = "CPR elements"
 * )
 */
class CprAddressElement extends CprLookupElement implements NemidElementPersonalInterface {

  /**
   * {@inheritdoc}
   */
  public function getPrepopulateFieldFieldKey() {
    return 'address';
  }

}
