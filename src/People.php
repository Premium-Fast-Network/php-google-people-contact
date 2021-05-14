<?php

namespace PremiumFastNetwork;

class People
{
    /**
     * Google People V1 URL
     */
    const PeopleV1 =
        'https://people.googleapis.com/v1';

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
            $this->scopes = [
                Scopes::USERINFO_PROFILE,
                Scopes::CONTACTS,
                Scopes::CONTACTS_READONLY,
            ];
        }
    }

    public function getContact($value)
    {

    }
    
    public function createContact($value)
    {

    }

    public function listContact($options)
    {
        return $this->client->request('GET', 'people/me/connections?' . http_build_query($options));
    }
}