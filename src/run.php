<?php
/**
 * Created by PhpStorm.
 * User: pengyu
 * Date: 2020/10/16
 * Time: 14:40
 */

require_once 'vendor/autoload.php';

if (php_sapi_name() != 'cli') {
    throw new Exception('please run this file in cli mode');
}

$server = new \Pengyu\DfaFilter\Server();
$server->run();