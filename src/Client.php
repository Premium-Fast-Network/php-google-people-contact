<?php

namespace PremiumFastNetwork;

use Exception;

class Client
{
    /**
     * Google OAuth URL
     */
    const OAUTH =
        'https://accounts.google.com/o/oauth2/auth';
    
    const OAUTH_TOKEN =
        'https://oauth2.googleapis.com/token';

    /**
     * Default Custom Headers Variable
     */
    private $headers;

    private $rootURL;

    /**
     * Default API Google Detail
     * 
     * - client_id
     * - client_secret
     * - response_type
     * - access_type
     * - redirect_uri
     * - prompt
     */
    private $api;

    /**
     * Default API Scopes
     */
    private $scopes;

    /**
     * Default Token Variable
     */
    private $token;

    public function __construct()
    {
        $this->scopes = [
            Scopes::USERINFO_PROFILE,
            Scopes::CONTACTS,
            Scopes::CONTACTS_READONLY,
        ];
    }

    public function setRootURL($value)
    {
        $this->rootURL = $value;
    }

    public function getRootURL()
    {
        return $this->rootURL;
    }

    /**
     * Set Custom Header
     */
    public function setHeaders($value = null)
    {
        // only set custom header if value not null
        if($value) {
            $this->headers = $value;
        } else {
            // set default header
            $headers   = [];
            $headers[] = 'Accept: application/json';
            $headers[] = 'Content-Type: application/json';
            
            // set token if exist
            if(!empty($this->token)) {
                $headers[] = 'Authorization: Bearer ' . $this->token['access_token'];
            }

            // set custom header
            $this->headers = $headers;
        }
    }

    /**
     * Get Custom Header
     */
    public function getHeader()
    {
        return $this->headers;
    }

    public function validateApi()
    {
        if(empty($this->api['client_id'])) {
            throw new Exception('Please Setup Client ID Before Continue..!');
        }

        if(empty($this->api['client_secret'])) {
            throw new Exception('Please Setup Client Secret Before Continue..!');
        }

        if(empty($this->api['response_type'])) {
            $this->api['response_type'] = 'code';
        }

        if(empty($this->api['access_type'])) {
            $this->api['access_type'] = 'offline';
        }

        if(empty($this->api['redirect_uri'])) {
            $this->api['redirect_uri'] = 'urn:ietf:wg:oauth:2.0:oob';
        }

        if(empty($this->api['prompt'])) {
            $this->api['prompt'] = 'select_account';
        }

        if(empty($this->scopes)) {
            throw new Exception('Please Setup Scopes Before Continue..!');
        }
    }

    public function validateToken()
    {
        if(empty($this->token['refresh_token'])) {
            throw new Exception('Please Set Refresh Token Before Continue..!');
        }

        if(empty($this->token['access_token'])) {
            // set header to url encode
            $this->setHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]);

            try {
                $request = $this->request('POST', self::OAUTH_TOKEN, [
                    'refresh_token' => $this->token['refresh_token'],
                    'client_id' => $this->api['client_id'],
                    'client_secret' => $this->api['client_secret'],
                    'grant_type' => 'refresh_token'
                ]);

                // setup token
                $response = json_decode($request, true);
                $this->token['access_token'] = $response['access_token'];

                // clean up headers
                $this->headers = null;

                return $this->token;
            } catch (Exception $e) {
                throw $e;
            }
        }
    }

    public function createAuthUrl()
    {
        // validate before request
        $this->validateApi();

        // Array of URL
        $url = [];
        $url[] = 'response_type=' . $this->api['response_type'];
        $url[] = 'access_type=' . $this->api['access_type'];
        $url[] = 'redirect_uri=' . rawurlencode($this->api['redirect_uri']);
        $url[] = 'client_id=' . $this->api['client_id'];
        $url[] = 'scope=' . rawurlencode(implode(' ', $this->scopes));
        $url[] = 'state';
        $url[] = 'prompt=' . $this->api['prompt'];

        // Convert Array and Const to String URL
        return self::OAUTH . '?' . implode('&', $url);
    }

    public function getTokenWithCode($code)
    {
        // set header to url encode
        $this->setHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded'
        ]);
        
        try {
            $request = $this->request('POST', self::OAUTH_TOKEN, [
                'code' => $code,
                'client_id' => $this->api['client_id'],
                'client_secret' => $this->api['client_secret'],
                'redirect_uri'=> 'urn:ietf:wg:oauth:2.0:oob',
                'grant_type' => 'authorization_code'
            ]);

            $this->token = $request;

            return $this->token;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getTokenWithRefreshToken()
    {
        $this->validateToken();
    }

    public function request($type, $url, $form = null)
    {
        // validate before request
        $this->validateApi();

        // validate header
        if(empty($this->headers)) {
            $this->setHeaders();
        }

        // build url request
        $buildURL = $url;
        if(strpos($url, 'https') === false) {
            $buildURL = $this->rootURL . '/' . $url;
        }

        // build curl instance
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $buildURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        // check action type
        if ($type == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $form);
        } elseif ($type == 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $form);
        } elseif ($type == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        }

        // running curl
        $result = curl_exec($ch);

        // throw error
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }

        // stop connection
        curl_close($ch);

        // return result request
        return $result;
    }

    public function setClientId($value)
    {
        $this->api['client_id'] = $value;
    }

    public function getClientId()
    {
        return $this->api['client_id'];
    }

    public function setClientSecret($value)
    {
        $this->api['client_secret'] = $value;
    }

    public function getClientSecret()
    {
        return $this->api['client_secret'];
    }

    public function setScopes($value)
    {
        $this->scopes = $value;
    }

    public function getScopes()
    {
        return $this->scopes;
    }

    public function setAccessToken($value)
    {
        $this->token['access_token'] = $value;
    }

    public function getAccessToken()
    {
        return $this->token['access_token'];
    }

    public function setRefreshToken($value)
    {
        $this->token['refresh_token'] = $value;
    }

    public function getRefreshToken()
    {
        return $this->token['refresh_token'];
    }

    public function setResponseType($value)
    {
        $this->api['response_type'] = $value;
    }

    public function getResponseType()
    {
        return $this->api['response_type'];
    }

    public function setAccessType($value)
    {
        $this->api['access_type'] = $value;
    }

    public function getAccessType()
    {
        return $this->api['access_type'];
    }

    public function setRedirectUri($value)
    {
        $this->api['redirect_uri'] = $value;
    }

    public function getRedirectUri()
    {
        return $this->api['redirect_uri'];
    }

    public function setPrompt($value)
    {
        $this->api['prompt'] = $value;
    }

    public function getPrompt()
    {
        return $this->api['prompt'];
    }   
}