<?php

$loadModules = array(
    'init',
    'input',
    'array',
    'mysql',
    'security',
    'config',
    'statement',
    'proxy',
);

$buffer = '';
ob_start();
foreach ($loadModules as $one)
{
  $path = realpath(sprintf('%s/%s.php', __DIR__, $one));
  if (file_exists($path))
  {
    require_once $path;
  }
}

if ('dev' == __ENVIRONMENT__)
{
  $buffer = ob_get_contents();
}

ob_end_clean();
echo $buffer;

function loadConfig($namespace)
{
  GLOBAL $CONFIG;
  
  return array_key_exists($namespace, $CONFIG) ? $CONFIG[$namespace] : array();
}

function loadGetConfig($namespace, $name)
{
  $namespace = loadConfig($namespace);
  
  return isset($namespace[$name]) ? $namespace[$name] : false;
}

function loadFile($path)
{
  if (file_exists($path))
  {
    return require_once $path;
  }
  else
  {
    throw404();
  }
  
  return false;
}

function loadAction($default)
{
  $action = trim(
    inputFromServer('PATH_INFO', $default, FILTER_SANITIZE_STRING),
    '/ .'
  );
  $action = $action ? : $default;
  
  $exploded = explode('/', $action);
  $file     = strtolower(array_shift($exploded));
  $other    = arrayAllToUcfirst($exploded);
  $fileName = sprintf('%s%s.php', $file, implode('', $other));
  $path     = realpath(__DIR__ . '/../pages/' . $fileName);
  
  return loadFile($path);
}

function dump($value, $exit = true)
{
  if ('dev' !== __ENVIRONMENT__) return false;
  
  var_dump($value);
  if ($exit) exit();
}

function throw404()
{
  header("HTTP/1.0 404 Not Found");
}