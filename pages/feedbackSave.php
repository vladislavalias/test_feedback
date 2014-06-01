<?php

inputSetSession('feedback', 'errors', false);

if (feedbackIsFormValid())
{
  $fileName = feedbackSaveFile(feedbackSubmitedFile());
  $formData = array_merge(feedbackSubmitedData(), array('file' => $fileName));
  feedbackSaveForm($formData);
}

proxyRedirectTo('/');

function feedbackSaveForm($formData)
{
  $formData['created_at'] = time();
  $formData['updated_at'] = time();
  
  mysqlInsert('feedback', $formData);
}

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

function feedbackRequiredFields()
{
  return array(
    'title',
    'email'
  );
}

function feedbackSubmitedFile()
{
  return isset($_FILES['feedback']) ? $_FILES['feedback'] : array();
}


function feedbackSubmitedData()
{
  return inputFromPost(
    'feedback',
    false,
    FILTER_DEFAULT,
    FILTER_REQUIRE_ARRAY
  );
}

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

function feedbackCreateDir($dir)
{
  if (!is_dir($dir))
  {
    mkdir($dir, 0777, true);
  }
  
  return $dir;
}

function feedbackParseFileName($filename)
{
  $dotPos = strripos($filename, '.');
  
  return array(
    substr($filename, 0, $dotPos),
    substr($filename, $dotPos + 1)
  );
}