<?php

namespace Wolfish; 

class User
{
    private $nick;
    private $userId;

    public function __construct($uid, $nick)
    {
        $this->nick = $nick;
        $this->userId = $uid;
    }

    private function isAllowed()
    {
        if (!empty(Config::ALLOWED_USERS)) {
            if (!in_array($this->nick, Config::ALLOWED_USERS) && !in_array($this->userId, Config::ALLOWED_USERS)) {
                return false;
            }
        } elseif (!empty(Config::BANNED_USERS)) {
            if (in_array($this->nick, Config::BANNED_USERS) || in_array($this->userId, Config::BANNED_USERS)) {
                return false;
            }
        }

        return true;
    }

    public function checkAccess()
    {
        if (!$this->isAllowed()) {
            return '{ "response_type": "ephemeral", "text": "'.Config::BANNED_TEXT.'" }';
        }

        return true;
    }
}
