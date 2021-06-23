<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\os2forms_nemid\Plugin\WebformElement\NemidElementPersonalInterface;

/**
 * CPR Floor element.
 *
 * @WebformElement(
 *   id = "cpr_floor_element",
 *   label = @Translation("CPR Floor Element"),
 *   description = @Translation("CPR Floor Element description"),
 *   category = @Translation("CPR elements")
 * )
 */
class CprFloorElement extends CprLookupElement implements NemidElementPersonalInterface {

  /**
   * {@inheritdoc}
   */
  public function getPrepopulateFieldFieldKey() {
    return 'floor';
  }

}
