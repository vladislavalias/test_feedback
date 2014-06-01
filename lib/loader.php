<?php

// загружаемые по-умолчанию модули в глобальную область.
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
//  если файл есть и все гуд то грузим.
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

/**
 * Получаем конфиги из области.
 * 
 * @global array $CONFIG
 * @param string $namespace
 * @return mixed
 */
function loadConfig($namespace)
{
  GLOBAL $CONFIG;
  
  return array_key_exists($namespace, $CONFIG) ? $CONFIG[$namespace] : array();
}

/**
 * Получаем конфиг значение из заданной области.
 * 
 * @param string $namespace
 * @param string $name
 * @return mixed
 */
function loadGetConfig($namespace, $name)
{
  $namespace = loadConfig($namespace);
  
  return isset($namespace[$name]) ? $namespace[$name] : false;
}

/**
 * Загружаем файл если он существует.
 * И если нет то выкидываем 404 статус.
 * 
 * @param string $path
 * @return boolean
 */
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

/**
 * Загружаем текущий экшн согласно строке запроса, или если
 * она пуста то согласно переданному дефолтовому.
 * 
 * @param string $default
 * @return boolean
 */
function loadCurrentAction($default)
{
  $action = trim(
    inputFromServer('PATH_INFO', $default, FILTER_SANITIZE_STRING),
    '/ .'
  );
  $action = $action ? : $default;
  
  return loadAction($action);
}

/**
 * Подгружаем указанный экшн.
 * По сути это алиас для функции loadAction.
 * 
 * @param string $action
 * @return boolean
 */
function loadForwardToAction($action)
{
  return loadAction($action);
}

/**
 * Загружаем указанный экшн с проверкой его доступности.
 * 
 * @param string $action
 * @return boolean
 */
function loadAction($action)
{
  $exploded = explode('/', $action);
  $file     = strtolower(array_shift($exploded));
  $other    = arrayAllToUcfirst($exploded);
  $fileName = sprintf('%s%s.php', $file, implode('', $other));
  $path     = realpath(__DIR__ . '/../pages/' . $fileName);
  
  return loadFile($path);
}

/**
 * Функция для дампа данных, не работающая в прод окружении.
 * 
 * @param mixed $value
 * @param boolean $exit
 * @return boolean
 */
function dump($value, $exit = true)
{
  if ('dev' !== __ENVIRONMENT__) return false;
  
  var_dump($value);
  if ($exit) exit();
}

/**
 * Выбрасываем 404 статус и зе энд, фолкс.
 */
function throw404()
{
  header("HTTP/1.0 404 Not Found");
  exit();
}