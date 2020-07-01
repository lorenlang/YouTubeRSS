<?php
/**
 * Created by PhpStorm.
 * User: llang
 * Date: 6/10/15
 * Time: 2:53 PM
 */

require_once 'vendor/adodb/adodb-php/adodb.inc.php';

$DB = NewADOConnection('mysqli');
$DB->Connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
