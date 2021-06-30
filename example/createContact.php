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
$save = $people->createContact('081223456789', 'FirstName', 'LastName');
$response = json_decode($save);

if($response && $response->resourceName) {
    echo "People ID: " . $response->resourceName . "\n";
    echo "People ETag: " . $response->etag . "\n";
    echo "Display Names: " . $response->names[0]->displayName . "\n";
    echo "Phone Number: " . $response->phoneNumbers[0]->value . "\n";
} else {
    print_r("Failed to Save Contact.!");
}