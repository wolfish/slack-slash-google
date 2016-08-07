<?php
namespace Wolfish;

use Wolfish\Config;
use Wolfish\Parameters;

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
        $params = $params->getCseParameters();

        try {
            $result = $this->_service->cse->listCse($text, $params);
        } catch(\Google_Service_Exception $e) {
            if($e->getCode() === 403)
                return '{ "text": "'.Config::GOOGLE_LIMIT_EXCEEDED.'" }';
            else
                return '{ "text": "'.$e->getMessage().'" }';
        }

        $items = $result->getItems();

        if (count($items) > 1) {
            foreach ($result->getItems() as $k => $item) {
                $responseText[] = $item->getLink();
                if($k == 19) break; // Slack limitation
            }
        } else {
            $responseText = $items[0]->getLink();
        }

        $response = array(
            'response_type' => Config::SLACK_RESPONSE_TYPE,
            'unfurl_media' => Config::SLACK_UNFURL_MEDIA,
            'unfurl_links' => Config::SLACK_UNFURL_LINKS
        );

        if (is_array($responseText)) {
            $response['text'] = Config::MULTIPLE_RESULTS_TEXT;
            foreach ($responseText as $num => $t) { 
                $response['attachments'][] = array('text' => ($num+1).'. '.$t."\n");
            }
        } else {
            $response['text'] = $responseText;
        }

        return json_encode($response);
    }
}
