<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
define('__ENVIRONMENT__', 'dev');

require_once realpath(__DIR__ . '/lib/loader.php');

loadAction('index/render/');