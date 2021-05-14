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
        $this->client = $client;
        $this->client->setRootURL(self::PeopleV1);
        $this->client->validateToken();
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