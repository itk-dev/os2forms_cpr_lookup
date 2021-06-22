<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\os2forms_nemid\Plugin\WebformElement\NemidElementPersonalInterface;

/**
 * CPR Side element.
 *
 * @WebformElement(
 *   id = "cpr_side_element",
 *   label = "CPR Side Element",
 *   description = "CPR Side Element description",
 *   category = "CPR elements"
 * )
 */
class CprSideElement extends CprLookupElement implements NemidElementPersonalInterface {

  /**
   * {@inheritdoc}
   */
  public function getPrepopulateFieldFieldKey() {
    return 'side';
  }

}
