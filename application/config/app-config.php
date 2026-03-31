<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Auto-detect Base URL with SSL support
|--------------------------------------------------------------------------
|
| Automatically detects the base URL based on the current request.
| Works for both domain root and subfolder installations.
|
*/

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
            (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') 
            ? 'https' : 'http';

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$path = isset($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : '';

// Ensure path doesn't end with /installer and remove trailing slashes
$path = rtrim(str_replace(['/installer', '\\'], '', $path), '/');

$base_url = rtrim($protocol . '://' . $host . $path, '/') . '/';

define('APP_BASE_URL', $base_url);

/*
|--------------------------------------------------------------------------
| Encryption Key
| IMPORTANT: Dont change this EVER
|--------------------------------------------------------------------------
|
| If you use the Encryption class, you must set an encryption key.
| See the user guide for more info.
|
| Auto updated added on install
*/

define('APP_ENC_KEY','b433f0ad1b3a721393476640ee95aeed');

/* Database credentials - Auto added on install */

/* The hostname of your database server. */
define('APP_DB_HOSTNAME','localhost');
/* The username used to connect to the database */
define('APP_DB_USERNAME','root');
/* The password used to connect to the database */
define('APP_DB_PASSWORD','');
/* The name of the database you want to connect to */
define('APP_DB_NAME','instafoliodbase');

/* Session Handler */

define('SESS_DRIVER','database');
define('SESS_SAVE_PATH','sessions');