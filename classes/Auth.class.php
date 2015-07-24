<?php

/**
 * Authentication class
 */

class Auth
{

  private static $_instance;  // singleton instance

  private $_currentUser;  // current signed in user object

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

  /**
   * Login a user
   *
   * @param string $email     Email address
   * @param string $password  Password
   * @return boolean          true if the new user record was saved successfully, false otherwise
   */
  public function login($email, $password)
  {
    $user = User::authenticate($email, $password);

    if ($user !== null) {

      $this->_currentUser = $user;

      // Store the user ID in the session
      $_SESSION['user_id'] = $user->id;

      // Regenerate the session ID to prevent session hijacking
      session_regenerate_id();

      return true;
    }

    return false;
  }

 /**
  * Get the current logged in user
  *
  * @return mixed  User object if logged in, null otherwise
  */
 public function getCurrentUser()
 {
   if ($this->_currentUser === null) {
     if (isset($_SESSION['user_id'])) {

       // Cache the object so that in a single request the data is loaded from the database only once.
       $this->_currentUser = User::findByID($_SESSION['user_id']);
     }
   }

   return $this->_currentUser;
 }


 /**
  * Boolean indicator of whether the user is logged in or not
  *
  * @return boolean
  */
 public function isLoggedIn()
 {
   return $this->getCurrentUser() !== null;
 }

}
