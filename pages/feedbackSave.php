<?php

if (feedbackIsFormValid())
{
  feedbackSaveForm();
}

function feedbackSaveForm()
{
  
}

function feedbackIsFormValid()
{
  $form     = inputFromPost(
    'form',
    false,
    FILTER_DEFAULT,
    FILTER_REQUIRE_ARRAY
  );
  $feedback = inputFromPost(
    'feedback',
    false,
    FILTER_DEFAULT,
    FILTER_REQUIRE_ARRAY
  );
  
  if (
    $form &&
    isset($form['submited']) &&
    securityIsValidForm($feedback, feedbackRequiredFields())
  )
  {
    return true;
  }
  
  return false;
}

function feedbackRequiredFields()
{
  return array(
    'title',
    'email'
  );
}