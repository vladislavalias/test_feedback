<?php
ini_set("display_errors", 0);
define('__ENVIRONMENT__', 'prod');

require_once realpath(__DIR__ . '/lib/loader.php');

loadCurrentAction('index/render/');