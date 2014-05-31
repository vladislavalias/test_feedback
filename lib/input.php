<?php

function inputFromPost($name, $default = false, $filter = FILTER_DEFAULT, $require = FILTER_REQUIRE_SCALAR)
{
  return filter_input(INPUT_POST, $name, $filter, $require) ? : $default;
}

function inputFromGet($name, $default = false, $filter = FILTER_DEFAULT)
{
  return filter_input(INPUT_GET, $name, $filter) ? : $default;
}

function inputFromServer($name, $default = false, $filter = FILTER_DEFAULT)
{
  return filter_input(INPUT_SERVER, $name, $filter) ? : $default;
}

function inputFromSession($name, $default = false, $filter = FILTER_DEFAULT)
{
  return filter_input(INPUT_SESSION, $name, $filter) ? : $default;
}