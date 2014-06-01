<?php

/**
 * Редирект на урл.
 * 
 * @param string $page
 */
function proxyRedirectTo($page)
{
  header(sprintf("Location: %s", $page));
  exit();
}