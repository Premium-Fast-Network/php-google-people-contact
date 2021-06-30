<?php

require_once(__DIR__ . './../vendor/autoload.php');

use PremiumFastNetwork\Client;
use PremiumFastNetwork\People;
use PremiumFastNetwork\Scopes;

// get data from file
$fileData = file_get_contents(__DIR__ . '/data.json');
$data = json_decode($fileData);

// setup offline client
$client = new Client();
$client->setClientId($data->googleClientID);
$client->setClientSecret($data->googleClientSecret);
$client->setRefreshToken($data->refreshToken);
$client->setScopes([
    Scopes::USERINFO_PROFILE,
    Scopes::CONTACTS,
    Scopes::CONTACTS_READONLY,
]);

$people = new People($client);
$response = $people->batchDeleteContact([
    'people/c7970798843045239796',
    'people/c7125974266199980934',
    'people/c9008410021235787598',
    'people/c3303563411911349587',
    'people/c8209155738909287553',
    'people/c4611136984342315318',
]);

print_r($response);