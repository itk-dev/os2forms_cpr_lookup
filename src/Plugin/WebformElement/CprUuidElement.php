<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\os2forms_nemid\Plugin\WebformElement\NemidElementPersonalInterface;

/**
 * CPR Uuid element.
 *
 * @WebformElement(
 *   id = "cpr_uuid_element",
 *   label = @Translation("CPR Uuid Element"),
 *   description = @Translation("CPR Uuid Element description"),
 *   category = @Translation("CPR elements")
 * )
 */
class CprUuidElement extends CprLookupElement implements NemidElementPersonalInterface {

  /**
   * {@inheritdoc}
   */
  public function getPrepopulateFieldFieldKey() {
    return 'uuid';
  }

}
