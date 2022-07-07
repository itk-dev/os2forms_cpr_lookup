<?php

namespace Drupal\os2forms_cpr_lookup\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\os2forms_cpr_lookup\CPR\CprServiceResult;
use Drupal\os2forms_nemlogin_openid_connect\Plugin\os2web\NemloginAuthProvider\OpenIDConnect;
use GuzzleHttp\Client;
use Http\Factory\Guzzle\RequestFactory;
use ItkDev\AzureKeyVault\Authorisation\VaultToken;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use ItkDev\AzureKeyVault\KeyVault\VaultSecret;
use ItkDev\Serviceplatformen\Certificate\AzureKeyVaultCertificateLocator;
use ItkDev\Serviceplatformen\Request\InvocationContextRequestGenerator;
use ItkDev\Serviceplatformen\Service\Exception\ServiceException;
use ItkDev\Serviceplatformen\Service\PersonBaseDataExtendedService;

/**
 * CPR Service.
 */
class CprService implements CprServiceInterface {

  /**
   * PersonBaseDataExtendedService.
   *
   * @var \ItkDev\Serviceplatformen\Service\PersonBaseDataExtendedService
   */
  private $personBaseDataExtendedService;

  /**
   * Constructor.
   */
  public function __construct(Client $guzzleClient, ConfigFactoryInterface $configFactory) {
    $config = $configFactory->get('os2forms_cpr_lookup');

    $httpClient = new GuzzleAdapter($guzzleClient);
    $requestFactory = new RequestFactory();

    $vaultToken = new VaultToken($httpClient, $requestFactory);

    $token = $vaultToken->getToken(
      $config->get('azure_tenant_id'),
      $config->get('azure_application_id'),
      $config->get('azure_client_secret')
    );

    $vault = new VaultSecret(
      $httpClient,
      $requestFactory,
      $config->get('azure_key_vault_name'),
      $token->getAccessToken()
    );

    $certificateLocator = new AzureKeyVaultCertificateLocator(
      $vault,
    // Name of the certificate.
      $config->get('azure_key_vault_secret'),
    // Version of the certificate.
      $config->get('azure_key_vault_secret_version')
    );

    $pathToWsdl = $config->get('service_contract');

    $options = [
      'local_cert' => $certificateLocator->getAbsolutePathToCertificate(),
      'passphrase' => $certificateLocator->getPassphrase(),
      'location' => $config->get('service_endpoint'),
    ];

    $soapClient = new \SoapClient($pathToWsdl, $options);

    $requestGenerator = new InvocationContextRequestGenerator(
      $config->get('service_agreement_uuid'),
      $config->get('user_system_uuid'),
      $config->get('service_uuid'),
      $config->get('user_uuid')
    );

    $this->personBaseDataExtendedService = new PersonBaseDataExtendedService($soapClient, $requestGenerator);
  }

  /**
   * {@inheritdoc}
   */
  public function search($cpr) {
    $response = $this->personBaseDataExtendedService->personLookup($cpr);
    return new CprServiceResult($response);
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
          $result = $this->search($cpr);
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
      $childrenArr = explode(' ', $cprData['children']);

      switch ($element['#cpr_output_type']) {
        case 'name':
          foreach ($childrenArr as $childCpr) {
            try {
              $child = $this->search($childCpr);
              $data = $child->toArray();
              $options[$data['name']] = $data['name'];
            } catch (ServiceException $e) {
            }
          }
          break;
        case 'cpr':
          foreach ($childrenArr as $childCpr) {
            try {
              $child = $this->search($childCpr);
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
