<?php
use Wolfish\Config;
use Wolfish\SlackEnum;
use Wolfish\User;
use Wolfish\GoogleRequest;

require_once 'vendor/autoload.php';
header('Content-type: application/json');

// Test request by calling script in console with argument
if (isset($argv[1])) {
    $_POST['text'] = $argv[1];
    $_POST['token'] = Config::SLACK_TOKEN;
}

if (mb_strlen(serialize($_POST), '8bit') > Config::MAXPOST) {
    echo '{ "response_type": "'.SlackEnum::RESPONSE_TYPE_PRIVATE.'", "text": "Input data too big" }';
    exit;
}

foreach ($_POST as $key => $val) {
    $_POST[$key] = htmlentities($val, ENT_QUOTES);
}

if ($_POST['token'] !== Config::SLACK_TOKEN) {
    echo '{ "response_type": "'.SlackEnum::RESPONSE_TYPE_PRIVATE.'", "text": "Invalid slack token" }';
    exit;
}

if (!isset($argv[1])) {
    $uid = $_POST['user_id'];
    $user = new User($uid, $_POST['user_name']);

    $access = $user->checkAccess();
    if ($access !== true) {
        echo $access;
    }
}

$result = new GoogleRequest($_POST['text']);
echo $result->listCseRequest();
if (isset($argv[1])) echo "\n";
