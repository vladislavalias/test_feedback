<?php

require_once realpath(__DIR__ . '/configMysql.php');

$CONFIG['upload']['dir'] = realpath(__DIR__.'/../upload');
$CONFIG['upload']['path'] = '/upload';