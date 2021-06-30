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
$lists = $people->listContact([
    'pageSize' => 10,
    'personFields' => 'names,phoneNumbers'
]);

$response = json_decode($lists);

if($response && count($response->connections) > 0) {
    foreach($response->connections as $list) {
        echo "People ID: " . $list->resourceName . "\n";
        echo "People ETag: " . $list->etag . "\n";
        echo "Display Names: " . $list->names[0]->displayName . "\n";
        echo "Phone Number: " . $list->phoneNumbers[0]->value . "\n";
    }
} else {
    print_r("Contact Not Found.!");
}