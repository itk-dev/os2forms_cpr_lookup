<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\os2forms_nemid\Plugin\WebformElement\NemidElementPersonalInterface;

/**
 * CPR Street element.
 *
 * @WebformElement(
 *   id = "cpr_street_element",
 *   label = @Translation("CPR Street Element"),
 *   description = @Translation("CPR Street Element description"),
 *   category = @Translation("CPR elements")
 * )
 */
class CprStreetElement extends CprLookupElement implements NemidElementPersonalInterface {

  /**
   * {@inheritdoc}
   */
  public function getPrepopulateFieldFieldKey(array &$element) {
    return 'street_name';
  }

}
