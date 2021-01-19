<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
require_once('vendor/autoload.php');
$f3 = Base::instance();

$f3->set('DEBUG', 3);
$f3->route('GET /', function() {
    echo 'Hello!';
});
$f3->run();


