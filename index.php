<?php
ini_set("display_errors", 0);
define('__ENVIRONMENT__', 'dev');

require_once realpath(__DIR__ . '/lib/loader.php');

loadAction('index/render/');