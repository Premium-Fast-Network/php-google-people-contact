<?php

namespace PremiumFastNetwork;

class People
{
    /**
     * Google People V1 URL
     */
    const PeopleV1 =
        'https://people.googleapis.com/v1';

    /**
     * Default Person Fields
     */
    const PersonFields = ['addresses', 'ageRanges', 'biographies', 'birthdays', 'calendarUrls', 'clientData', 'coverPhotos', 'emailAddresses', 'events', 'externalIds', 'genders', 'imClients', 'interests', 'locales', 'locations', 'memberships', 'metadata', 'miscKeywords', 'names', 'nicknames', 'occupations', 'organizations', 'phoneNumbers', 'photos', 'relations', 'sipAddresses', 'skills', 'urls', 'userDefined',];

    private $client;

    public function __construct(Client $client)
    {
        // set client
        $this->client = $client;
        // set default root url
        $this->client->setRootURL(self::PeopleV1);
        // set scopes default
        if(empty($this->client->getScopes())) {
            $this->client->setScopes([
                Scopes::USERINFO_PROFILE,
                Scopes::CONTACTS,
                Scopes::CONTACTS_READONLY,
            ]);
        }
        // fetch access_token from refresh token if not exist
        $this->client->validateToken();
    }

    /**
     * Get Contact Detail
     * 
     * @param string $resourceName
     */
    public function getContact($resourceName)
    {
        return $this->client->request(
            'GET',
            $resourceName.'?personFields=' .
            implode(',', self::PersonFields)
        );
    }
    
    /**
     * Create New Contact
     * 
     * @param string $phone
     * @param string $firstName
     * @param string $lastName
     * @param array @options
     */
    public function createContact($phone, $firstName, $lastName, $options = null)
    {
        $newData = [
            'names' => [
                [
                    'givenName' => $firstName,
                    'familyName' => $lastName
                ]
            ],
            'phoneNumbers' => [
                [
                    'value' => $phone
                ]
            ]
        ];

    
        // merge data
        $mergeData = $options ? array_merge($options, $newData) : $newData;


        // fetch request
        return $this->client->request(
            'POST',
            'people:createContact?personFields=' . implode(',', self::PersonFields), 
            json_encode($mergeData)
        );
    }

    public function deleteContact($resourceName)
    {
        return $this->client->request(
            'DELETE',
            $resourceName.':deleteContact'
        );
    }

    public function listContact($options)
    {
        return $this->client->request(
            'GET',
            'people/me/connections?personFields=' .
                implode(',', self::PersonFields) .
                '&'. http_build_query($options)
            );
    }

    public function batchCreateContact()
    {

    }

    public function batchUpdateContact()
    {

    }

    public function batchDeleteContact()
    {

    }
}