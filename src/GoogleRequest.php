<?php

namespace Wolfish;

class GoogleRequest
{
    private $client;
    private $service;
    private $params;
    private $text;

    public function __construct(string $text)
    {
        $this->client = new \Google_Client();
        $this->client->setApplicationName(Config::GOOGLE_APP_NAME);
        $this->client->setDeveloperKey(Config::GOOGLE_DEV_KEY);

        $this->params = new Parameters($text);

        $this->text = $text;

        $this->service = new \Google_Service_Customsearch($this->client);

        return $this;
    }

    public function makeCseRequest()
    {
        try {
            $result = $this->service->cse->listCse($this->text, $this->params->getCseParameters());
        } catch (\Google_Service_Exception $e) {
            if ($e->getCode() === 403) {
                return '{ "text": "'.Config::GOOGLE_LIMIT_EXCEEDED.'" }';
            }
            
            return '{ "text": "'.$e->getMessage().'" }';
        }

        return $result;
    }

    public function listCseRequest()
    {
        $result = $this->makeCseRequest();

        if (!$result instanceof \Google_Service_Customsearch_Search) {
            return $result;
        }

        $items = $result->getItems();

        $response = new Response($items);

        return $response->getSlackResponse($this->params);
    }
}
