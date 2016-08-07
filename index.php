<?php
use Wolfish\Config;
use Wolfish\GoogleRequest;

require_once 'vendor/autoload.php';
header('Content-type: application/json');

// Test request by calling script in console with argument
if (isset($argv[1])) {
    $_POST['text'] = $argv[1];
    $_POST['token'] = Config::SLACK_TOKEN;
}

if (mb_strlen(serialize($_POST), '8bit') > Config::MAXPOST) {
    echo '{ "text": "Input data too big" }';
    exit;
}

foreach ($_POST as $key => $val) {
    $_POST[$key] = htmlentities($val, ENT_QUOTES);
}

if ($_POST['token'] !== Config::SLACK_TOKEN) {
    echo '{ "text": "Invalid slack token" }';
    exit;
}

$result = new GoogleRequest();
echo $result->listCseRequest($_POST['text']);
if (isset($argv[1])) echo "\n";
