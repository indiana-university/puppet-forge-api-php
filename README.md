# puppet-forge-php-api
A PHP interface to the Puppet Forge REST API

![GitHub Workflow Status](https://img.shields.io/github/workflow/status/indiana-university/puppet-forge-api-php/PHP%20Composer?style=flat-square)
![Travis (.org)](https://img.shields.io/travis/indiana-university/puppet-forge-api-php?style=flat-square)
![Scrutinizer code quality (GitHub/Bitbucket)](https://img.shields.io/scrutinizer/quality/g/indiana-university/puppet-forge-api-php?style=flat-square)
![Packagist](https://img.shields.io/packagist/l/indiana-university/puppet-forge-api-php?style=flat-square)

## Requirements
This library requires at least PHP 7.3

## Usage
```php
use Edu\Iu\Uits\Webtech\ForgeApi\ForgeApi;

$api = new ForgeApi('YOUR API KEY HERE');

/**
 * User operations
 */

// Listing users
$api->user()->list([
    // See official API documentation for parameters
    'limit' => 20,
]);

// Fetching a user
$api->user('puppetlabs')->fetch();

/**
* Module operations
 */

// Fetch a module
$api->module('puppetlabs-apache')->fetch();

// Delete a module
$api->module('puppetlabs-apache')->delete('Broken code');

// Deprecate a module
$api->module('puppetlabs-apache')->deprecate(
    'No longer maintained',
    'puppet-nginx'
);

// List modules
$api->module()->list(['limit' => 20]);

/**
 * Release operations
 */

// List releases
$api->release()->list(['limit' => 20]);

// Create a release
$api->release('puppetlabs-apache')->create('base64 encoded string');

// Fetch a release
$api->release('puppetlabs-apache-4.0.0')->fetch();

// Delete a release
$api->release('puppetlabs-apache-4.0.0')->delete('bugs');
```

## Limitations
1. Currently module release plans are not supported. This is strictly
because of time limitations. They will _probably_ be supported in the future.

2. It is not currently possible to customize the user agent.
