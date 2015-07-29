<?php

/**
 * Utilities class
 */

class Util
{

  const SERVER_HOST = '/sandbox/bazinga';

  /**
   * Redirect to a different page
   *
   * @param string $url  The relative URL
   * @return void
   */
  public static function redirect($url)
  {
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/sandbox/bazinga' . $url);
    exit;
  }

}
