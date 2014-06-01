<?php

/**
 * Проверка валидности формы.
 * 
 * @param array $formData
 * @param array $fields
 * @return boolean|array
 */
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

/**
 * Стандартный валидатор поля.
 * 
 * @param mixed $value
 * @return boolean
 */
function securityValidateDefault($value)
{
  return (bool)$value;
}

/**
 * Валидатор имейла.
 * 
 * @param string $value
 * @return string
 */
function securityValidateEmail($value)
{
  return filter_var($value, FILTER_VALIDATE_EMAIL);
}

/**
 * Проверка поля на пустоту.
 * 
 * @param array $data
 * @param string $field
 * @return boolean
 */
function securityIsNotEmpty($data, $field)
{
  return isset($data[$field]) && $data[$field];
}

/**
 * Рендер ошибок из области.
 * 
 * @param string $namespace
 * @return string
 */
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