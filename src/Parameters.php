<?php

namespace Wolfish;

class Parameters
{
    private $request;
    private $parsed = false;

    public function __construct(string $request)
    {
        $this->request['text'] = $request;
        $this->parseRequest();
    }

    private function parseRequest()
    {
        $this->parsed = true;
        $regex = '/#(\d|gif|image|link|one|multi|priv|pub|color|gray|mono)/i';
        $cmds = preg_split($regex, $this->request['text'], null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        foreach ($cmds as $k => $cmd) {
            $cmd = trim($cmd);
            if (empty($cmd) || !isset($cmds[$k + 1])) {
                continue;
            }

            if (is_numeric($cmd)) {
                $this->request['index'] = $cmd;
            }

            switch ($cmd) {
            case 'gif':
                $this->request['searchType'] = 'image';
                $this->request['fileType'] = $cmd;
                break;

            case 'image':
                $this->request['searchType'] = 'image';
                $this->request['imgColorType'] = 'color';
                break;

            case 'gray':
            case 'mono':
                $this->request['searchType'] = 'image';
                $this->request['imgColorType'] = $cmd;
                break;

            case 'link':
                $this->request['searchType'] = 'link'; // internal, not a google value!
                break;

            case 'one':
                $this->request['num'] = 1;
                break;

            case 'multi':
                $this->request['num'] = 0;
                break;

            case 'pub':
                $this->request['slack']['response_type'] = 'in_channel';
                break;

            case 'priv':
                $this->request['slack']['response_type'] = 'ephemeral';
                break;
            }
        }

        $this->request['text'] = trim($cmds[count($cmds) - 1]);
    }

    public function getCseParameters()
    {
        $params = Config::GOOGLE_SEARCH_PARAMETERS;

        $params['q'] = $this->request['text'];
        $params['cx'] = Config::GOOGLE_CX_KEY;
        $params['start'] = (isset($this->request['index']) ? $this->request['index'] : 1);

        if (isset($this->request['num'])) {
            if ($this->request['num'] > 0) {
                $params['num'] = $this->request['num'];
            } elseif (isset($params['num'])) {
                unset($params['num']);
            }
        }

        if (isset($this->request['searchType'])) {
            if ($this->request['searchType'] === 'link') {
                unset($params['searchType']);
            } else {
                $params['searchType'] = $this->request['searchType'];
                $params['imgColorType'] = $this->request['imgColorType'];
            }
        }

        if (isset($this->request['fileType'])) {
            $params['fileType'] = $this->request['fileType'];
        }

        return $params;
    }

    public function getSlackParameters()
    {
        $slackParams = Config::SLACK_RESPONSE_PARAMETERS;

        if (isset($this->request['slack']['response_type'])) {
            $slackParams['response_type'] = $this->request['slack']['response_type'];
        }

        return $slackParams;
    }

    public function isImageSearch()
    {
        return isset($this->request['searchType']) && ($this->request['searchType'] === 'image');
    }
}
