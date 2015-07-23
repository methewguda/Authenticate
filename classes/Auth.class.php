<?php

/**
 * Authentication class
 */

class Auth
{

  private static $_instance;  // singleton instance

  private function __construct() {}  // disallow creating a new object of the class with new Auth()

  private function __clone() {}  // disallow cloning the class


  /**
   * Initialisation
   *
   * @return void
   */
  public static function init()
  {
    // Start or resume the session
    session_start();
  }


  /**
   * Get the singleton instance
   *
   * @return Auth
   */
  public static function getInstance()
  {
    if (static::$_instance === NULL) {
      static::$_instance = new Auth();
    }

    return static::$_instance;
  }

}
