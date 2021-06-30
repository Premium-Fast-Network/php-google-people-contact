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

// build multiple contact
$people->batchBuildContact('0812123456780', 'First 0', 'End');
$people->batchBuildContact('0812123456781', 'First 1', 'End');
$people->batchBuildContact('0812123456782', 'First 2', 'End');
$people->batchBuildContact('0812123456783', 'First 3', 'End');
$people->batchBuildContact('0812123456784', 'First 4', 'End');
$people->batchBuildContact('0812123456784', 'First 5', 'End');

// execute and get response
$execute = $people->batchCreateContact();
$response = json_decode($execute);

if(isset($response->createdPeople) && !empty($response->createdPeople) && count($response->createdPeople) > 0) {
    foreach($response->createdPeople as $contact) {
        echo "People ID: " . $contact->person->resourceName . "\n";
        echo "Display Names: " . $contact->person->names[0]->displayName . "\n";
        echo "Phone Number: " .$contact->person->phoneNumbers[0]->value . "\n";
    }
}