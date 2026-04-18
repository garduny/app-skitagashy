<?php
if (php_sapi_name() !== 'cli') exit("CLI Only\n");
if (!isset($_SERVER['REQUEST_URI'])) $_SERVER['REQUEST_URI'] = '/cron';
if (!isset($_SERVER['HTTP_USER_AGENT'])) $_SERVER['HTTP_USER_AGENT'] = 'CronJob';
if (!isset($_SERVER['REMOTE_ADDR'])) $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
if (!isset($_SERVER['HTTP_HOST'])) $_SERVER['HTTP_HOST'] = 'localhost';
if (!isset($_SERVER['SERVER_NAME'])) $_SERVER['SERVER_NAME'] = 'localhost';
ini_set('memory_limit','512M');
set_time_limit(0);
error_reporting(E_ALL);
require_once __DIR__ . '/../init.php';
date_default_timezone_set('Asia/Baghdad');
execute(" SET time_zone = '+03:00' ");