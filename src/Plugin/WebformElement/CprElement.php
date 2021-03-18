<?php


namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;


use Drupal\webform\Annotation\WebformElement;
use Drupal\webform\Plugin\WebformElement\TextField;

/**
 * @WebformElement(
 *   id = "cpr_element",
 *   label = "CPR Element",
 *   description = "CPR Element description",
 *   category = "CPR elements"
 * )
 */
class CprElement extends TextField
{

}
