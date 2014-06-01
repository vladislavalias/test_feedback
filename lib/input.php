<?php

function inputFromPost($name, $default = false, $filter = FILTER_DEFAULT, $require = FILTER_REQUIRE_SCALAR)
{
  return filter_input(INPUT_POST, $name, $filter, $require) ? : $default;
}

function inputFromGet($name, $default = false, $filter = FILTER_DEFAULT, $require = FILTER_REQUIRE_SCALAR)
{
  return filter_input(INPUT_GET, $name, $filter, $require) ? : $default;
}

function inputFromServer($name, $default = false, $filter = FILTER_DEFAULT, $require = FILTER_REQUIRE_SCALAR)
{
  return filter_input(INPUT_SERVER, $name, $filter, $require) ? : $default;
}

function inputFromSession($name, $default = false)
{
  return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
}

function inputSetSession($namespace, $name, $value)
{
  return $_SESSION[$namespace][$name] = $value;
}