<?php

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

function arrayAllToUcfirst($array)
{
  return array_map(
    function($one){
      return ucfirst($one);
    },
    $array
  );
}