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
   * Logout a user
   *
   * @return void
   */
  public function logout()
  {
    // Forget the remembered login, if set
    if (isset($_COOKIE['remember_token'])) {

      // Delete the record from the database - note the hash
      $this->getCurrentUser()->forgetLogin(sha1($_COOKIE['remember_token']));

      // Delete the cookie with the value of the token. Setting the expiration date to
      // a time in the past (in this case, one hour ago) will cause the browser to delete
      // the cookie.
      setcookie('remember_token', '', time() - 3600);
    }

    // Remove all session variables and destroy the session
    $_SESSION = array();
    session_destroy();
  }

  /**
   * Login a user
   *
   * @param string $email     Email address
   * @param string $password  Password
   * @return boolean          true if the new user record was saved successfully, false otherwise
   */
  public function login($email, $password, $remember_me)
  {
    $user = User::authenticate($email, $password);

    if ($user !== null) {

      $this->_currentUser = $user;

      $this->_loginUser($user);

      // Remember the login
      if ($remember_me) {

        $expiry = time() + 60 * 60 * 24 * 30;  // set to expire 30 days from now

        $token = $user->rememberLogin($expiry);

        // Set the "remember me" cookie with the token value and expiry
        if ($token !== false) {
          setcookie('remember_token', $token, $expiry);
        }
      }

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
      } else {

        // Login from the remember me cookie if set
        $this->_currentUser = $this->_loginFromCookie();
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

 /**
 * Redirect to the login page if no user is logged in.
 *
 * @return void
 */
 public function requireLogin()
 {
   if ( ! $this->isLoggedIn()) {

     // Save the requested page to return to after logging in
     $url = $_SERVER['REQUEST_URI'];
     if ( ! empty($url)) {
       $_SESSION['return_to'] = $url;
     }

     Util::redirect('/pages/signin.php');
   }
 }

 /**
  * Redirect to the home page if a user is logged in.
  *
  * @return void
  */
 public function requireGuest()
 {
   if ($this->isLoggedIn()) {
     Util::redirect('/index.php');
   }
 }

 /**
  * Send the user password reset email
  *
  * @param string $email  Email address
  * @return void
  */
public function sendPasswordReset($email)
{
  $user = User::findByEmail($email);

  if ($user !== null) {

    if ($user->startPasswordReset()) {

      // Note hardcoded protocol
      $url = 'http://'.$_SERVER['HTTP_HOST'].'/sandbox/bazinga/pages/reset_password.php?token=' . $user->password_reset_token;

      $body = <<<EOT
<p>Please click on the following link to reset your password.</p>
<p><a href="$url">$url</a></p>
EOT;

      //$body = "Please click on the following link to reset your password.<br><br>" . '<p><a href="' . $url . '">' . $url . '</a></p>';

      Mail::send($user->name, $user->email, 'Password reset', $body);
    }
  }
}

 /**
   * Log the user in from the remember me cookie
   *
   * @return mixed  User object if logged in correctly from the cookie, or null otherwise
   */
  private function _loginFromCookie() {

    if (isset($_COOKIE['remember_token']))
    {
      // Find user that has the token set (the token is hashed in the database)
      $user = User::findByRememberToken(sha1($_COOKIE['remember_token']));

      if ($user !== null) {
        $this->_loginUser($user);
        return $user;
      }
    }
  }


  /**
   * Login the user to the session
   *
   * @param User $user  User object
   * @return void
   */
  private function _loginUser($user) {

    // Store the user ID in the session
    $_SESSION['user_id'] = $user->id;

    // Regenerate the session ID to prevent session hijacking
    session_regenerate_id();
  }

}
