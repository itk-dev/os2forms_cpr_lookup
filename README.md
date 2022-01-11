# OS2Forms CPR Lookup

Query the Danish for CPR register for Drupal Webforms.

## Installation

Require it with composer:

```shell
composer require "itk-dev/os2forms_cpr_lookup"
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

This module provides functionality for querying the danish CPR register and
showing the result in webforms.

This can be done in two ways:

1. By adding a query element which when changed performs a query and populates
   other elements with the result.
2. By requiring personal NemLog-in login on the form (set Edit form > Settings >
   Third party settings > OS2Forms > OS2Forms NemID settings > Webform type to
   “Personal”) and having elements pre-filled based on data fetched from the CPR
   service.

**Note**: You cannot use both methods on the same form, i.e. you cannot have
some elements filled by a dynamic lookup based on an “CPR Element” and have
other elements filled based on an authenticated users’ data.

The elements provided:

* CPR Element - Element which queries the Danish CPR register when changed.
* CPR Name Element - This is populated with the name from the above mentioned query result.
* CPR Address Element - This is populated with the address from the result.
* CPR Street Element - This is filled with the name of the street from the result.
* CPR House Number Element - This is filled with the house number from the result.
* CPR Side Element - This is filled with the side from the result.
* CPR Floor Element - This is filled with the floor from the result.
* CPR Postal Code Element - This is filled with the postal code from the result.
* CPR City Element - This is filled with the city from the result.

## Coding standards

Check coding standards (run `composer install` to install the required tools):

```shell
composer coding-standards-check
```

Apply coding standards:

```shell
composer coding-standards-apply
```
