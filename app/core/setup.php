<?php

//require our files, remember should be relative to index.php
require '../app/core/Router.php';
require '../app/models/Model.php';
require '../app/controllers/Controller.php';
require '../app/controllers/MainController.php';
require '../app/models/Post.php';
require '../app/controllers/PostController.php';
require '../app/models/User.php';
require '../app/controllers/UserController.php';

//set up env variables
$envFile = '../.env';
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
    define('DBHOST', $env['DBHOST']);
    define('DBNAME', $env['DBNAME']);
    define('DBUSER', $env['DBUSER']);
    define('DBPASS', $env['DBPASS']);
    define('DBPORT', $env['DBPORT'] ?? '3306');
} else {
    // fallback for production (Heroku)
    define('DBHOST', getenv('DBHOST'));
    define('DBNAME', getenv('DBNAME'));
    define('DBUSER', getenv('DBUSER'));
    define('DBPASS', getenv('DBPASS'));
    define('DBPORT', getenv('DBPORT') ?: '3306');
}

// session/cookie setup 
ini_set('session.gc_maxlifetime', 3600); // set session timeout to 1 hour (3600 seconds)
ini_set('session.cookie_lifetime', 3600); // set cookie lifetime to 1 hour

// security params
session_set_cookie_params([
    'lifetime' => 3600,          // 1 hour 
    'path' => '/',               // available across entire domain
    'secure' => true,            // HTTPS only & more secure 
    'httponly' => true,          
    'samesite' => 'Strict'       
]);

// start if not started 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
