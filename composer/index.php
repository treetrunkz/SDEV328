<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
$f3 = Base::instance();
$f3->set('DEBUG', 3);

$f3->run();

echo "whats up!"

?>