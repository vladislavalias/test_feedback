<?php

feedbackResetFormErrors();

if (feedbackIsFormValid())
{
  $fileName = feedbackSaveFile(feedbackSubmitedFile());
  $formData = array_merge(feedbackSubmitedData(), array('file' => $fileName));
  feedbackSaveForm($formData);
  
  inputSetFlashMessage('Message sended.');
  feedbackResetFormErrors();
  proxyRedirectTo(inputGenerateUrl('/'));
}
else
{
  loadForwardToAction('index/render');
}

/**
 * Сохраняем нашу форму добавляя данные о создании
 * и обновлении.
 * 
 * @param array $formData
 */
function feedbackSaveForm($formData)
{
  $formData['created_at'] = time();
  $formData['updated_at'] = time();
  
  mysqlInsert('feedback', $formData);
}

/**
 * Сохраняем файл возвращая путь к нему где сохранили.
 * 
 * @return string
 */
function feedbackSaveFile()
{
  $file = feedbackSubmitedFile();
  
  $result = false;
  if ($file && $file['tmp_name']['file'])
  {
    if ($dir = feedbackCreateDir(loadGetConfig('upload', 'dir')))
    {
      list($fileName, $extension) = feedbackParseFileName($file['name']['file']);
      $fullFileName = sprintf(
        '%s.%s',
        md5($fileName . rand(0, 10000)),
        $extension
      );
      
      if (move_uploaded_file(
        $file['tmp_name']['file'],
        $dir . '/' . $fullFileName)
      )
      {
        $result = loadGetConfig('upload', 'path') . '/' . $fullFileName;
      }
    }
  }
  
  return $result;
}

/**
 * Проверяем форму на валидность и если нет то сохраняем ошибки.
 * 
 * @return boolean
 */
function feedbackIsFormValid()
{
  $errors = array();
  if (feedbackIsSubmited())
  {
    $isValid = securityIsValidForm(
      feedbackSubmitedData(),
      feedbackRequiredFields()
    );
    
    if (true === $isValid)
    {
      return true;
    }
    else
    {
//      there errors
      $errors = $isValid;
    }
  }
  else
  {
    $errors[] = 'Form is not submitted.';
  }
  
  inputSetSession('feedback', 'errors', $errors);
  
  return false;
}

/**
 * Получаем список обязательных полей.
 * 
 * @return array
 */
function feedbackRequiredFields()
{
  return array(
    'title',
    'email'
  );
}

/**
 * Проверка отправлен ли файл и возвращаем его данные.
 * 
 * @return boolean
 */
function feedbackSubmitedFile()
{
  return isset($_FILES['feedback']) ? $_FILES['feedback'] : array();
}

/**
 * Получаем отправленные данные формы.
 * 
 * @return boolean
 */
function feedbackSubmitedData()
{
  return inputFromPost(
    'feedback',
    false,
    FILTER_DEFAULT,
    FILTER_REQUIRE_ARRAY
  );
}

/**
 * Проверяем отправлена ли форма.
 * 
 * @return boolean
 */
function feedbackIsSubmited()
{
  $form     = inputFromPost(
    'form',
    false,
    FILTER_DEFAULT,
    FILTER_REQUIRE_ARRAY
  );
  
  return isset($form['submited']);
}

/**
 * Создаем папки для загрузки файлов.
 * 
 * @param string $dir
 * @return string
 */
function feedbackCreateDir($dir)
{
  if (!is_dir($dir))
  {
    mkdir($dir, 0777, true);
  }
  
  return $dir;
}

/**
 * Парсим имя и возвращаем его название и расширение.
 * 
 * @param string $filename
 * @return array
 */
function feedbackParseFileName($filename)
{
  $dotPos = strripos($filename, '.');
  
  return array(
    substr($filename, 0, $dotPos),
    substr($filename, $dotPos + 1)
  );
}

/**
 * Сбрасываем ошибки нашей формы.
 * 
 * @return boolean
 */
function feedbackResetFormErrors()
{
  return inputSetSession('feedback', 'errors', false);
}