# Google People (Contact) PHP SDK

This is an SDK for Google People (known as Google Contact)

You can check all the information on the official google people page at the following link: https://developers.google.com/people/api/rest

## Install

```
composer require premiumfastnet/google-people-contact
```

## Example

### Generate Auth URL

```php
<?php

require 'vendor/autoload.php';

use PremiumFastNetwork\Client;
use PremiumFastNetwork\Scopes;

$client = new Client();
$client->setClientId('googleClientID');
$client->setClientSecret('googleClientSecrets');
$client->setScopes([
    Scopes::USERINFO_PROFILE,
    Scopes::CONTACTS,
    Scopes::CONTACTS_READONLY,
]);

$authUrl = $client->createAuthUrl();

print_r($authUrl);
```

### Get Access Token From Code

```php
$authCode = $client->getTokenWithCode('ResponseCode');
print_r($authCode);
```

### List All Contact

```php
$people = new People($client);
$lists = $people->listContact([
    'pageSize' => 100,
    'personFields' => 'names,phoneNumbers'
]);
print_r($lists);
```
