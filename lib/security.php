<?php

function securityIsValidForm($formData, $fields)
{
  if (!$formData || !sizeof($formData)) return false;
  if (!$fields) return true;
  
  $valid  = true;
  $errors = array();
  
  foreach ($fields as $field)
  {
    if (securityIsNotEmpty($formData, $field))
    {
      $function = sprintf('securityValidate%s', ucfirst($field));

      if(!function_exists($function))
      {
        $function = 'securityValidateDefault';
      }
      $result = $function($formData[$field]);
      
      if (!$result)
      {
        $errors[] = $field . ' not valid.';
      }
      
      $valid = $valid && $result;
    }
    else
    {
      $valid    = false;
      $errors[] = $field . ' required.';
    }
  }
  
  return $valid ? : $errors;
}

function securityValidateDefault($value)
{
  return (bool)$value;
}

function securityValidateEmail($value)
{
  return filter_var($value, FILTER_VALIDATE_EMAIL);
}

function securityIsNotEmpty($data, $field)
{
  return isset($data[$field]) && $data[$field];
}

function securityRenderErrors($namespace)
{
  $namespace  = inputFromSession($namespace, array());
  $errors     = array();
  
  if ($namespace && isset($namespace['errors']) && $namespace['errors'])
  {
    $errors = $namespace['errors'];
  }
  
  return implode('<br />', $errors);
}