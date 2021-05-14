<?php

namespace PremiumFastNetwork;

class People
{
    /**
     * Google People V1 URL
     */
    const PeopleV1 =
        'https://people.googleapis.com/v1';

    const PersonFields = ['addresses', 'ageRanges', 'biographies', 'birthdays', 'calendarUrls', 'clientData', 'coverPhotos', 'emailAddresses', 'events', 'externalIds', 'genders', 'imClients', 'interests', 'locales', 'locations', 'memberships', 'metadata', 'miscKeywords', 'names', 'nicknames', 'occupations', 'organizations', 'phoneNumbers', 'photos', 'relations', 'sipAddresses', 'skills', 'urls', 'userDefined',];

    private $client;

    public function __construct(Client $client)
    {
        // set client
        $this->client = $client;
        // set default root url
        $this->client->setRootURL(self::PeopleV1);
        // fetch access_token from refresh token if not exist
        $this->client->validateToken();
        // set scopes default
        if(empty($this->client->getScopes())) {
            $this->client->setScopes([
                Scopes::USERINFO_PROFILE,
                Scopes::CONTACTS,
                Scopes::CONTACTS_READONLY,
            ]);
        }
    }

    public function getContact($value)
    {
        return $this->client->request('GET', $value.'?personFields=' . implode(',', self::PersonFields));
    }
    
    public function createContact($value)
    {

    }

    public function listContact($options)
    {
        return $this->client->request('GET', 'people/me/connections?personFields=' . implode(',', self::PersonFields) .'&'. http_build_query($options));
    }
}