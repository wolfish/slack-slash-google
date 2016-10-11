<?php

namespace Wolfish;

class GoogleRequest
{
    private $client;
    private $service;

    public function __construct()
    {
        $this->client = new \Google_Client();
        $this->client->setApplicationName(Config::GOOGLE_APP_NAME);
        $this->client->setDeveloperKey(Config::GOOGLE_DEV_KEY);

        $this->service = new \Google_Service_Customsearch($this->client);

        return $this;
    }

    public function listCseRequest(string $text)
    {
        $params = new Parameters($text);
        $parameters = $params->getCseParameters();

        try {
            $result = $this->service->cse->listCse($text, $parameters);
        } catch (\Google_Service_Exception $e) {
            if ($e->getCode() === 403) {
                return '{ "text": "'.Config::GOOGLE_LIMIT_EXCEEDED.'" }';
            }
            
            return '{ "text": "'.$e->getMessage().'" }';
        }

        $items = $result->getItems();

        $response = new Response($items);

        return $response->getSlackResponse($params);
    }
}
