<?php

namespace Wolfish;

class Response
{
    private $_items;

    public function __construct($items)
    {
        $this->_items = $items;
    }

    public function getSlackResponse(Parameters $params)
    {
        if (count($this->_items) > 1) {
            foreach ($this->_items as $k => $item) {
                $responseText[] = $item->getLink();
                if ($k == 19) {
                    break;
                } // Slack limitation
            }
        } elseif ($this->_items[0] instanceof \Google_Service_Customsearch_Result) {
            $responseText = $this->_items[0]->getLink();
        } else {
            $responseText = Config::GOOGLE_NO_RESULT;
        }

        $response = $params->getSlackParameters();

        if (is_array($responseText)) {
            $response['text'] = Config::MULTIPLE_RESULTS_TEXT;
            foreach ($responseText as $num => $t) {
                $response['attachments'][] = array('text' => ($num + 1).'. '.$t."\n");
            }
        } else {
            $response['text'] = $responseText;
        }

        return json_encode($response);
    }
}