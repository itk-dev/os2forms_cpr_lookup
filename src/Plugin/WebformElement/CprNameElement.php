<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\os2forms_nemid\Plugin\WebformElement\NemidElementPersonalInterface;

/**
 * @WebformElement(
 *   id = "cpr_name_element",
 *   label = "CPR Name Element",
 *   description = "CPR Name Element description",
 *   category = "CPR elements"
 * )
 */
class CprNameElement extends CprLookupElement implements NemidElementPersonalInterface {

  /**
   * {@inheritdoc}
   */
  public function getPrepopulateFieldFieldKey() {
    return 'name';
  }

}
