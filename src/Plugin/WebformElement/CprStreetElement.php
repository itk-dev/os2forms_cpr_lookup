<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\os2forms_nemid\Plugin\WebformElement\NemidElementPersonalInterface;

/**
 * @WebformElement(
 *   id = "cpr_street_element",
 *   label = "CPR Street Element",
 *   description = "CPR Street Element description",
 *   category = "CPR elements"
 * )
 */
class CprStreetElement extends CprLookupElement implements NemidElementPersonalInterface {

  /**
   * {@inheritdoc}
   */
  public function getPrepopulateFieldFieldKey() {
    return 'street_name';
  }

}
