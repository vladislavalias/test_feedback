<?php

function securityIsValidForm($formData, $fields)
{
  if (!$formData || !sizeof($formData)) return false;
  if (!$fields) return true;
  
  $valid = false;
  
  if (securityIsNotEmpty($formData, $fields))
  {
    foreach ($fields as $fieldName)
    {
      $function = sprintf('securityValidate%s', ucfirst($fieldName));
      $valid    = true;
      
      if(!function_exists($function))
      {
        $function = 'securityValidateDefault';
      }
      
      $valid = $valid && $function($formData[$fieldName]);
    }
  }
  
  return $valid;
}

function securityValidateDefault($value)
{
  return (bool)$value;
}

function securityValidateEmail($value)
{
  return filter_var($value, FILTER_VALIDATE_EMAIL);
}

function securityIsNotEmpty($data, $fields)
{
  $notEmpty = true;
  
  $fields     = array_flip($fields);
  $intersect  = array_intersect_key($data, $fields);
  $diff       = array_diff(
                  array_intersect_key($data, $fields),
                  array(false, null, '')
                );
  $notEmpty   = $notEmpty && sizeof($fields) == sizeof($intersect);
  $notEmpty   = $notEmpty && sizeof($fields) == sizeof($diff);

  return $notEmpty;
}