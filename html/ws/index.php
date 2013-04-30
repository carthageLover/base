<?php

//-- webservice root
define('BASE_DIR', realpath(__DIR__ . '/../../php/WebService'));

//-- config using src/app.php 
$app = require_once (BASE_DIR . '/src/app.php');

// debug mode: error reports ON. set to false on prod.
$app['debug'] = true;

// delete later, not used
$app['auth.user'] = AUTH_USER;
$app['auth.pass'] = AUTH_PASS;

$app->run();
/*
echo "base dir: " . BASE_DIR . "<br>";
echo BASE_DIR . '/src/app.php';
die();*/
