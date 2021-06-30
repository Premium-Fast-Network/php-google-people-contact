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
$people->batchBuildUpdateContact(
    'people/c7970798843045239796',
    '%EiMBAj0DBQYHCAk+Cgs/DA0ODxBAExQVFjUZNDchIiMkJSYnLhoEAQIFByIMakFIRmI3bURLWnc9',
    '0812123456780',
    'First 0 Update 1',
    'End'
);

// execute and get response
$execute = $people->batchUpdateContact();
$response = json_decode($execute);

if(isset($response->updateResult) && !empty($response->updateResult)) {
    foreach($response->updateResult as $contact) {
        echo "People ID: " . $contact->person->resourceName . "\n";
        echo "People ETag: " . $contact->person->etag . "\n";
        echo "Display Names: " . $contact->person->names[0]->displayName . "\n";
        echo "Phone Number: " .$contact->person->phoneNumbers[0]->value . "\n";
    }
}