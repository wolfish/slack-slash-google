<?php
namespace Wolfish;

use Wolfish\Config;

class User
{
    private $_nick;
    private $_userId;

    public function __construct($id, $nick)
    {
        $this->_nick = $nick;
        $this->_userId = $id;
    }

    private function _isAllowed()
    {
        if (!empty(Config::ALLOWED_USERS)) {
            if (!in_array($this->_nick, Config::ALLOWED_USERS) && !in_array($this->_userId, Config::ALLOWED_USERS)) {
                return false;
            }
        } elseif (!empty(Config::BANNED_USERS)) {
            if (in_array($this->_nick, Config::BANNED_USERS) || in_array($this->_userId, Config::BANNED_USERS)) {
                return false;
            }
        }

        return true;
    }

    public function checkAccess()
    {
        if (!$this->_isAllowed()) {
            return '{ "response_type": "ephemeral", "text": "'.Config::BANNED_TEXT.'" }';
        }

        return true;
    }

}
