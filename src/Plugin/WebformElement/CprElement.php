<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\webform\Plugin\WebformElement\TextField;

/**
 * CPR Element.
 *
 * @WebformElement(
 *   id = "cpr_element",
 *   label = @Translation("CPR Lookup Element"),
 *   description = @Translation("Performs CPR lookup when value changes and fills in onther CPR data fields on the form"),
 *   category = @Translation("CPR elements")
 * )
 */
class CprElement extends TextField {

}
