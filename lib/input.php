<?php

/**
 * Достаем из поста.
 * 
 * @param string $name
 * @param mixed $default
 * @param integer $filter
 * @param integer $require
 * @return mixed
 */
function inputFromPost($name, $default = false, $filter = FILTER_DEFAULT, $require = FILTER_REQUIRE_SCALAR)
{
  return filter_input(INPUT_POST, $name, $filter, $require) ? : $default;
}

/**
 * Достаем из гета.
 * 
 * @param string $name
 * @param mixed $default
 * @param integer $filter
 * @param integer $require
 * @return mixed
 */
function inputFromGet($name, $default = false, $filter = FILTER_DEFAULT, $require = FILTER_REQUIRE_SCALAR)
{
  return filter_input(INPUT_GET, $name, $filter, $require) ? : $default;
}

/**
 * Достаем из сервера.
 * 
 * @param string $name
 * @param mixed $default
 * @param integer $filter
 * @param integer $require
 * @return mixed
 */
function inputFromServer($name, $default = false, $filter = FILTER_DEFAULT, $require = FILTER_REQUIRE_SCALAR)
{
  return filter_input(INPUT_SERVER, $name, $filter, $require) ? : $default;
}

/**
 * Достаем из сессии.
 * 
 * @param string $name
 * @param mixed $default
 * @return mixed
 */
function inputFromSession($name, $default = false)
{
  return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
}

/**
 * Вставляем что то в сессию в заданную область.
 *
 * @param string $namespace
 * @param string $name
 * @param mixed $value
 * @return mixed
 */
function inputSetSession($namespace, $name, $value)
{
  return $_SESSION[$namespace][$name] = $value;
}

/**
 * Генерируем ссылку в зависимости от окружения.
 * 
 * @param string $url
 * @return string
 */
function inputGenerateUrl($url)
{
  if ('dev' === __ENVIRONMENT__)
  {
    if ($script = trim(inputFromServer('SCRIPT_NAME'), '/ .'))
    {
      $url = sprintf('/%s/%s', $script, trim($url, '/ .'));
    }
  }
  
  return $url;
}

/**
 * Сохраняем сообщение для пользователя.
 * 
 * @param string $message
 * @return string
 */
function inputSetFlashMessage($message)
{
  return inputSetSession('message', 'feedback', $message);
}

/**
 * Получаем сообщение для пользователя.
 * 
 * @param string $name
 * @return string
 */
function inputGetFlashMessage($name)
{
  $message    = false;
  $namespace  = inputFromSession('message', false);
  if ($namespace && isset($namespace[$name]) && $namespace[$name])
  {
    $message = $namespace[$name];
    inputSetFlashMessage(false);
  }
  
  return $message;
}