# OS2Forms CPR Lookup

Query the Danish for CPR register for Drupal Webforms.

## Installation

Require it with composer:
```shell
composer require "itk-dev/os2forms-cpr-lookup"
```

Enable it with drush:
```shell
drush pm:enable os2forms_cpr_lookup
```

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

## Usage

This module provides functionality for querying the danish CPR register and showing the result.
In general terms you use it by adding a query element, which when changed performs a query and
populates other elements with the result.

The elements provided:

* CPR Element - Element which queries the Danish CPR register when changed.
* CPR Name Element - This is populated with the name from the above mentioned query result.
* CPR Address Element - This is populated with the address from the above mentioned query result.
