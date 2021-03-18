# OS2Forms CPR Lookup

## Usage

Add the following configuration:

```php
$config['os2forms_cpr_lookup'] = [
  'azure_tenant_id' => '',
  'azure_application_id' => '',
  'azure_client_secret' => '',

  'azure_key_vault_name' => '',
  'azure_key_vault_secret' => '',
  'azure_key_vault_secret_version' => '',

  'service_agreement_uuid' => '',
  'user_system_uuid' => '',
  'user_uuid' => '',

  'service_uuid' => '',
  'service_endpoint' => '',
  'service_contract' => dirname(DRUPAL_ROOT) . '/vendor/itk-dev/serviceplatformen/resources/person-base-data-extended-service-contract/wsdl/context/PersonBaseDataExtendedService.wsdl',
];
```
