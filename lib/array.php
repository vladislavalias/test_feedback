<?php

/**
 * Добавляем ко всем эелементам массива профикс.
 * 
 * @param array $array
 * @param string $prefix
 * @return array
 */
function arrayAddPrefixKeys($array, $prefix)
{
  $array = !is_array($array) ? array($array) : $array;
  
  $result = array();
  foreach ($array as $key => $value)
  {
    $newKey = sprintf('%s%s', $prefix, $key);
    $result[$newKey] = $value;
  }
  
  return $result;
}

/**
 * Делаем все элементы массива с большой буквы.
 * 
 * @param array $array
 * @return array
 */
function arrayAllToUcfirst($array)
{
  return array_map(
    function($one){
      return ucfirst($one);
    },
    $array
  );
}