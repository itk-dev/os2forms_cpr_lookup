<?php

namespace Drupal\os2forms_cpr_lookup\Service;

use Drupal\os2forms_cpr_lookup\Service\CprServiceInterface;
use Drupal\os2forms_nemlogin_openid_connect\Plugin\os2web\NemloginAuthProvider\OpenIDConnect;
use ItkDev\Serviceplatformen\Service\Exception\ServiceException;

class FormHelper {

  /**
   * The CPR service.
   *
   * @var \Drupal\os2forms_cpr_lookup\Service\CprServiceInterface
   */
  private $cprService;

  /**
   * Constructor.
   *
   * @param CprServiceInterface $cprService
   */
  public function __construct(CprServiceInterface $cprService) {
    $this->cprService = $cprService;
  }
  /**
   * Prepare form state data.
   *
   * @param OpenIDConnect $plugin
   *   The Nemlogin auth provider plugin ID
   * @return array|null
   *   CPR data.
   */
  public function prepareFormStateCprData(OpenIDConnect $plugin) {
    if ($plugin->isAuthenticated()) {
      try {
        $cpr = $plugin->fetchValue('cpr');
        if ($cpr) {
          $result = $this->cprService->search($cpr);
          $data = $result->toArray();
          // Add data for the CPR value element.
          $data['cpr'] = $cpr;
          // Merge in some values from the NemID login provider.
          $data += array_filter(
            array_map(
              [$plugin, 'fetchValue'],
              [
                'pid' => 'pid',
                // Will replace PID in the future https://migrering.nemlog-in.dk/nemlog-in-broker/privat-tjenesteudbyder/opslagstjenester/erstatning-til-pid-rid-uuid/
                'uuid' => 'uuid',
              ]
            )
          );

          return $data;
        }
      }
      catch (ServiceException $serviceException) {
        // @todo Log this?
      }
    }
    return NULL;
  }

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
  public function setChildSelectOptions(array $cprData, array $element) {
    $options = [];
    if(!empty($cprData['children'])) {
      switch ($element['#cpr_output_type']) {
        case 'name':
          foreach ($cprData['children'] as $childCpr) {
            try {
              $child = $this->cprService->search($childCpr);
              $data = $child->toArray();
              $options[$data['name']] = $data['name'];
            } catch (ServiceException $e) {
            }
          }
          break;
        case 'cpr':
          foreach ($cprData['children'] as $childCpr) {
            try {
              $child = $this->cprService->search($childCpr);
              $data = $child->toArray();
              $options[$childCpr] = $data['name'];
            } catch (ServiceException $e) {
            }
          }
          break;
        default:
      }
    }
    return $options;
  }
}
