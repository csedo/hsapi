<?php

namespace hscore;

class RequestGenerator
{
    private const REQUEST_METHOD = 'POST';
    private const API_URL = 'https://api.hifi-station.hu/v2/';

    private string $username;
    private string $password;
    private string $api_key;

    private string $request;
    private string $response_type = 'json';
    public bool $disablePrettyHeader = false;

    public function __construct($request = 'products')
    {
        $this->request = $request;
    }

    public function setOutputFormat($response_type)
    {
        if(!in_array($response_type, ['json', 'xml'])) {
            throw new \Exception('Invalid response type');
        }

        $this->response_type = $response_type;
    }

    private function getRequestMethod()
    {
        return self::REQUEST_METHOD;
    }

    private function getApiUrl()
    {
        return self::API_URL;
    }

    private function generateHeader()
    {
        if(!$this->disablePrettyHeader){
            if($this->response_type == 'xml'){
                header('Content-Type: application/xml');
            } else {
                header('Content-Type: application/json');
            }
        }

        $options = [
            'http' => [
                'method' => $this->getRequestMethod(),
                'header' => [
                    'Authorization: Basic ' . base64_encode($this->username . ':' . $this->password),
                    'X-API-Key: ' . $this->api_key
                ]
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ];

        return stream_context_create($options);
    }

    /**
     * @return string
     */
    public function send()
    {
        try {
            $this->requirementsCheck();
            $header = $this->generateHeader();
            $url = $this->getApiUrl();
            $response = file_get_contents($url.$this->request.'.'.$this->response_type, false, $header);
        } catch (\Exception $e) {
            $response = $e->getMessage();
        }
        return $response;
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $api_key
     * @return void
     */
    public function authenticate(string $username, string $password, string $api_key)
    {
        $this->username = $username;
        $this->password = $password;
        $this->api_key = $api_key;
    }

    private function requirementsCheck(){
        if(version_compare(PHP_VERSION, '7.1.0', '<')) {
            throw new \Exception('PHP version must be >= 7.1.0');
        }
        if(!extension_loaded('openssl')) {
            throw new \Exception('OpenSSL extension must be enabled.');
        }
    }
}