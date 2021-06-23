<?php

namespace Drupal\os2forms_cpr_lookup\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\os2forms_cpr_lookup\CPR\CprServiceResult;
use GuzzleHttp\Client;
use Http\Factory\Guzzle\RequestFactory;
use ItkDev\AzureKeyVault\Authorisation\VaultToken;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use ItkDev\AzureKeyVault\KeyVault\VaultSecret;
use ItkDev\Serviceplatformen\Certificate\AzureKeyVaultCertificateLocator;
use ItkDev\Serviceplatformen\Request\InvocationContextRequestGenerator;
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

}
