<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\os2forms_nemid\Plugin\WebformElement\NemidElementPersonalInterface;

/**
 * CPR Side element.
 *
 * @WebformElement(
 *   id = "cpr_side_element",
 *   label = @Translation("CPR Side Element"),
 *   description = @Translation("CPR Side Element description"),
 *   category = @Translation("CPR elements")
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
