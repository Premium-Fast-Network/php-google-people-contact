<?php

namespace PremiumFastNetwork;

use Exception;

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
     * Get Contact Detail by People ID
     * 
     * @see https://developers.google.com/people/api/rest/v1/people/get
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
     * @see https://developers.google.com/people/api/rest/v1/people/createContact
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

    /**
     * Delete Contact by People ID
     * 
     * @see https://developers.google.com/people/api/rest/v1/people/deleteContact
     * 
     * @param string $resourceName
     */
    public function deleteContact($resourceName)
    {
        return $this->client->request(
            'DELETE',
            $resourceName.':deleteContact'
        );
    }

    /**
     * Lists All Contact
     * 
     * @see https://developers.google.com/people/api/rest/v1/people.connections/list
     * 
     * @param array $options
     */
    public function listContact($options)
    {
        return $this->client->request(
            'GET',
            'people/me/connections?personFields=' .
                implode(',', self::PersonFields) .
                '&'. http_build_query($options)
            );
    }

    /**
     * Bulk Create Contact
     * Max: 200 Contact/Request
     * Limited to 10 parallel requests per user
     * 
     * @see https://developers.google.com/people/api/rest/v1/people/batchCreateContacts
     * 
     * @param array $lists
     */
    public function batchCreateContact($lists)
    {
        // count array
        if (count($lists) > 200) {
            throw new Exception("Maximum 200 Contact.!");
        }

        $dataCreate = [];
        $dataCreate['contacts'] = [];

        foreach($lists as $list) {
            $dataCreate['contacts'][]['contactPerson'] = $list;
        }

        return $dataCreate;
    }

    /**
     * Bulk Update Contact
     * Max: 200 Contact/Request
     * Limited to 10 parallel requests per user
     * 
     * @see https://developers.google.com/people/api/rest/v1/people/batchUpdateContacts
     * 
     * @param array $lists
     */
    public function batchUpdateContact($lists)
    {
        // count array
        if (count($lists) > 200) {
            throw new Exception("Maximum 200 Contact.!");
        }

        //
    }

    /**
     * Bulk Delete Contact
     * Max: 200 Contact/Request
     * Limited to 10 parallel requests per user
     * 
     * @see https://developers.google.com/people/api/rest/v1/people/batchDeleteContacts
     * 
     * @param array $lists
     */
    public function batchDeleteContact($lists)
    {
        // count array
        if (count($lists) > 200) {
            throw new Exception("Maximum 200 Contact.!");
        }
        
        // array data
        $dataDelete = [
            'resourceNames' => $lists
        ];

        // return response
        return $this->client->request(
            'POST',
            'people:batchDeleteContacts',
            json_encode($dataDelete)
        );
    }
}