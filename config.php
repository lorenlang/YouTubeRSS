<?php
/**
 * Created by PhpStorm.
 * User: llang
 * Date: 6/2/15
 * Time: 3:40 PM
 */

// function __autoload($name) {
//     list($ns, $class) = explode('\\', $name);
//     include_once __DIR__ . "/classes/$ns/$class.php";
// }
require __DIR__.'/vendor/autoload.php';



function l($stuff)
{
    $dev_log = '/tmp/ytrss.log';
    $stuff   = var_export($stuff, TRUE) . "\n" . str_repeat('-', 30) . "\n";
    error_log($stuff, 3, $dev_log);
}




//use Channel\Channel;


/*
 *   This fixes some issues with the DOMDocument LoadHTML method
 */
libxml_use_internal_errors(TRUE);

define('FILE_PATH', '/web/lorenlang.com/');
define('FILE_NAME', 'YouTubeRSS.xml');
define('OUTPUT_FILE', FILE_PATH . FILE_NAME);
define('FEED_URL' , 'http://lorenlang.com/' . FILE_NAME);

define('STORAGE_PATH', __DIR__.'/storage');


/*
 *   URL TEMPLATES CONSTANTS
 */
define('CHANNEL_URL', 'https://www.youtube.com/[URLTYPE]/[URLNAME]/videos?view=0');
define('VIDEO_URL', 'https://www.youtube.com/watch?v=[VIDEO_ID]');
define('IMAGE_URL', 'https://i.ytimg.com/vi/[VIDEO_ID]/mqdefault.jpg');


/*
 *   DATABASE CONFIGURATION CONSTANTS
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'ytrss');
define('DB_USER', 'ytrss');
define('DB_PASS', '3463101ac49b2be9fa68aec1c9b9325c');
define('DB_DSN', 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME);

