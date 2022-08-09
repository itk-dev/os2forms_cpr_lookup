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
}
