<?php

namespace Wolfish;

class GoogleRequest
{
    private $_client;
    private $_service;

    public function __construct()
    {
        $this->_client = new \Google_Client();
        $this->_client->setApplicationName(Config::GOOGLE_APP_NAME);
        $this->_client->setDeveloperKey(Config::GOOGLE_DEV_KEY);

        $this->_service = new \Google_Service_Customsearch($this->_client);

        return $this;
    }

    public function listCseRequest(string $text)
    {
        $params = new Parameters($text);
        $parameters = $params->getCseParameters();

        try {
            $result = $this->_service->cse->listCse($text, $parameters);
        } catch (\Google_Service_Exception $e) {
            if ($e->getCode() === 403) {
                return '{ "text": "'.Config::GOOGLE_LIMIT_EXCEEDED.'" }';
            } else {
                return '{ "text": "'.$e->getMessage().'" }';
            }
        }

        $items = $result->getItems();

        $response = new Response($items);

        return $response->getSlackResponse($params);
    }
}
