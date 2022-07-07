<?php

namespace Drupal\os2forms_cpr_lookup\Service;

use Drupal\os2forms_nemlogin_openid_connect\Plugin\os2web\NemloginAuthProvider\OpenIDConnect;

/**
 * CPR Service interface.
 */
interface CprServiceInterface {

  /**
   * Performs a call on the Person Base Data Extended service.
   *
   * @param string $cpr
   *   The CPR number to search for.
   *
   * @return \Drupal\os2forms_cpr_lookup\CPR\CprServiceResult
   *   The CPR Service Result.
   *
   * @throws \ItkDev\Serviceplatformen\Service\Exception\ServiceException
   */
  public function search($cpr);

  /**
   * Prepare form state data.
   *
   * @param OpenIDConnect $plugin
   *   The Nemlogin auth provider plugin ID
   * @return array|null
   *   CPR data.
   */
  public function prepareFormStateCprData(OpenIDConnect $plugin);

  /**
   * Set options in child selection elements Select/Radios.
   *
   * @param array $cprData
   *   CPR data
   * @param array $element
   *   The webform element
   * @return array
   *   A key/value list of options.
   */
  public function setChildSelectOptions(array $cprData, array $element);
}
