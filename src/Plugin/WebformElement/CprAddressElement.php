<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\os2forms_nemid\Plugin\WebformElement\NemidElementPersonalInterface;

/**
 * CPR Address element.
 *
 * @WebformElement(
 *   id = "cpr_address_element",
 *   label = @Translation("CPR Address Element"),
 *   description = @Translation("CPR Address Element description"),
 *   category = @Translation("CPR elements")
 * )
 */
class CprAddressElement extends CprLookupElement implements NemidElementPersonalInterface {

  /**
   * {@inheritdoc}
   */
  public function getPrepopulateFieldFieldKey(array &$element) {
    return 'address';
  }

}
