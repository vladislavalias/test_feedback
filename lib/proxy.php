<?php

function proxyRedirectTo($page)
{
  header(sprintf("Location: %s", $page));
}