<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\os2forms_nemid\Plugin\WebformElement\NemidElementPersonalInterface;

/**
 * CPR Value Element.
 *
 * @WebformElement(
 *   id = "cpr_value_element",
 *   label = @Translation("CPR Value Element"),
 *   description = @Translation("CPR Value Element description"),
 *   category = @Translation("CPR elements")
 * )
 */
class CprValueElement extends CprLookupElement implements NemidElementPersonalInterface {

  /**
   * {@inheritdoc}
   */
  public function getPrepopulateFieldFieldKey() {
    return 'cpr';
  }

}
