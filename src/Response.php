<?php

namespace Wolfish;

class Response
{
    private $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function getSlackResponse(Parameters $params)
    {
        if (count($this->items) > 1) {
            foreach ($this->items as $k => $item) {
                $responseText[] = $item;
                if ($k == 19) {
                    break;
                } // Slack limitation
            }
        } elseif ($this->items[0] instanceof \Google_Service_Customsearch_Result) {
            $responseText = $this->items[0];
        } else {
            $responseText = Config::GOOGLE_NO_RESULT;
        }

        $response = $params->getSlackParameters();

        if (is_array($responseText)) {
            $response['text'] = Config::MULTIPLE_RESULTS_TEXT;
            foreach ($responseText as $num => $t) {
                $response['attachments'][] = array('text' => ($num + 1).'. '.$t->getTitle()."\n".$t->getLink());
            }
        } else {
            if ($params->isImageSearch()) {
                $response['text'] = $responseText->getLink();
            } else {
                $response['text'] = $responseText->getTitle()."\n".$responseText->getLink();
            }
        }

        return json_encode($response);
    }
}
