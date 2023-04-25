<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\os2forms_nemid\Plugin\WebformElement\NemidElementPersonalInterface;

/**
 * CPR Pid element.
 *
 * @WebformElement(
 *   id = "cpr_pid_element",
 *   label = @Translation("CPR Pid Element"),
 *   description = @Translation("CPR Pid Element description"),
 *   category = @Translation("CPR elements")
 * )
 */
class CprPidElement extends CprLookupElement implements NemidElementPersonalInterface {

  /**
   * {@inheritdoc}
   */
  public function getPrepopulateFieldFieldKey(array &$element) {
    return 'pid';
  }

}
