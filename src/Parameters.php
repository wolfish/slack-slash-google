<?php

namespace Wolfish;

class Parameters
{
    private $_request;
    private $_parsed = false;

    public function __construct(string $request)
    {
        $this->_request['text'] = $request;
    }

    private function _parseRequest()
    {
        $this->_parsed = true;
        $regex = '/#(\d|gif|image|link|one|multi|priv|pub)/i';
        $cmds = preg_split($regex, $this->_request['text'], null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        foreach ($cmds as $k => $cmd) {
            $cmd = trim($cmd);
            if (empty($cmd) || !isset($cmds[$k + 1])) {
                continue;
            }

            if (is_numeric($cmd)) {
                $this->_request['index'] = $cmd;
            }

            switch ($cmd) {
            case 'gif':
                $this->_request['searchType'] = 'image';
                $this->_request['fileType'] = 'gif';
                break;

            case 'image':
                $this->_request['searchType'] = 'image';
                break;

            case 'link':
                $this->_request['searchType'] = 'link'; // internal, not a google value!
                break;

            case 'one':
                $this->_request['num'] = 1;
                break;

            case 'multi':
                $this->_request['num'] = 0;
                break;

            case 'pub':
                $this->_request['slack']['response_type'] = 'in_channel';
                break;

            case 'priv':
                $this->_request['slack']['response_type'] = 'ephemeral';
                break;
            }
        }

        $this->_request['text'] = trim($cmds[count($cmds) - 1]);
    }

    public function getCseParameters()
    {
        $params = Config::GOOGLE_SEARCH_PARAMETERS;

        if (!$this->_parsed) {
            $this->_parseRequest();
        }

        $params['q'] = $this->_request['text'];
        $params['cx'] = Config::GOOGLE_CX_KEY;
        $params['start'] = (isset($this->_request['index']) ? $this->_request['index'] : 1);
        if (isset($this->_request['num'])) {
            if ($this->_request['num'] > 0) {
                $params['num'] = $this->_request['num'];
            } else {
                if (isset($params['num'])) {
                    unset($params['num']);
                }
            }
        }
        if (isset($this->_request['searchType'])) {
            if ($this->_request['searchType'] === 'link') {
                unset($params['searchType']);
            } else {
                $params['searchType'] = $this->_request['searchType'];
            }
        }
        if (isset($this->_request['fileType'])) {
            $params['fileType'] = $this->_request['fileType'];
        }

        return $params;
    }

    public function getSlackParameters()
    {
        $slackParams = Config::SLACK_RESPONSE_PARAMETERS;

        if (!$this->_parsed) {
            $this->_parseRequest();
        }

        if (isset($this->_request['slack']['response_type'])) {
            $slackParams['response_type'] = $this->_request['slack']['response_type'];
        }

        return $slackParams;
    }
}
