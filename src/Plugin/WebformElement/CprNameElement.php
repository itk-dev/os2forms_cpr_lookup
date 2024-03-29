<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\os2forms_nemid\Plugin\WebformElement\NemidElementPersonalInterface;

/**
 * CPR Name element.
 *
 * @WebformElement(
 *   id = "cpr_name_element",
 *   label = @Translation("CPR Name Element"),
 *   description = @Translation("CPR Name Element description"),
 *   category = @Translation("CPR elements")
 * )
 */
class CprNameElement extends CprLookupElement implements NemidElementPersonalInterface {

  /**
   * {@inheritdoc}
   */
  public function getPrepopulateFieldFieldKey(array &$element) {
    return 'name';
  }

}
