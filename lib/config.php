<?php

// загружаем мускл конфиги, вынесены для облегчения потом установки через инсталл
require_once realpath(__DIR__ . '/configMysql.php');

$CONFIG['upload']['dir'] = realpath(__DIR__.'/../upload');
$CONFIG['upload']['path'] = '/upload';