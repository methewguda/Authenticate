<?php

/**
 * User class
 */

class User
{

  public $errors;

  /**
   * Authenticate a user by email and password
   *
   * @param string $email     Email address
   * @param string $password  Password
   * @return mixed            User object if authenticated correctly, null otherwise
   */
  public static function authenticate($email, $password)
  {
    $user = static::findByEmail($email);

    if ($user !== null) {

      // Check the hashed password stored in the user record matches the supplied password
      if (Hash::check($password, $user->password)) {
        return $user;
      }
    }
  }

  /**
   * Find the user with the specified ID
   *
   * @param string $id  ID
   * @return mixed      User object if found, null otherwise
   */
  public static function findByID($id)
  {
    try {

      $db = Database::getInstance();

      $stmt = $db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
      $stmt->execute([':id' => $id]);
      $user = $stmt->fetchObject('User');

      if ($user !== false) {
        return $user;
      }

    } catch(PDOException $exception) {

      error_log($exception->getMessage());
    }
  }


 /**
  * Find the user with the specified email address
  *
  * @param string $email  email address
  * @return mixed         User object if found, null otherwise
  */
  public static function findByEmail($email)
  {
    try {

      $db = Database::getInstance();

      $stmt = $db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
      $stmt->execute([':email' => $email]);
      $user = $stmt->fetchObject('User');

      if ($user !== false) {
        return $user;
      }

    } catch(PDOException $exception) {

      error_log($exception->getMessage());
    }
  }

  /**
   * Signup a new user
   *
   * @param array $data  POST data
   * @return User
   */
  public static function signup($data)
  {
    // Create a new user model and set the attributes
    $user = new static();

    $user->name = $data['name'];
    $user->email = $data['inputEmail'];
    $user->password = $data['inputPassword'];

    if ($user->isValid()) {

      // Generate a random token for activation and base64 encode it so it's URL safe
      $token = base64_encode(uniqid(rand(), true));
      $hashed_token = sha1($token);

      try {

        $db = Database::getInstance();

        $stmt = $db->prepare('INSERT INTO users (name, email, password, activation_token) VALUES (:name, :email, :password, :token)');
        $stmt->bindParam(':name', $user->name);
        $stmt->bindParam(':email', $user->email);
        $stmt->bindParam(':password', Hash::make($user->password));
        $stmt->bindParam(':token', $hashed_token);
        $stmt->execute();

        // Send activation email
        $user->_sendActivationEmail($token);

      } catch(PDOException $exception) {

        // Log the exception message
        error_log($exception->getMessage());
      }
    }

    return $user;
  }

  /**
   * Signup a new user
   *
   * @param array $data  POST data
   * @return User
   */
  public static function updateProfile($data)
  {
    // Create a new user model and set the attributes
    $user = new static();

    $user->name = $data['name'];
    $user->email = $data['email'];
    $user->phone = $data['phone'];

    try {

      $db = Database::getInstance();

      $stmt = $db->prepare('UPDATE users SET phone = :phone WHERE name = :name');
      $stmt->bindParam(':name', $user->name);
      $stmt->bindParam(':phone', $user->phone);
      $stmt->execute();

    } catch(PDOException $exception) {

      // Log the exception message
      error_log($exception->getMessage());
    }

    return $user;
  }

  /**
   * Find the user by remember token
   *
   * @param string $token  token
   * @return mixed         User object if found, null otherwise
   */
  public static function findByRememberToken($token)
  {
    try {

      $db = Database::getInstance();

      $stmt = $db->prepare('SELECT u.* FROM users u JOIN remembered_logins r ON u.id = r.user_id WHERE token = :token');
      $stmt->execute([':token' => $token]);
      $user = $stmt->fetchObject('User');

      if ($user !== false) {
        return $user;
      }

    } catch(PDOException $exception) {

      error_log($exception->getMessage());
    }
  }

  /**
   * Deleted expired remember me tokens
   *
   * @return integer  Number of tokens deleted
   */
  public static function deleteExpiredTokens()
  {
    try {

      $db = Database::getInstance();

      $stmt = $db->prepare("DELETE FROM remembered_logins WHERE expires_at < '" . date('Y-m-d H:i:s') . "'");
      $stmt->execute();

      return $stmt->rowCount();

    } catch(PDOException $exception) {

      // Log the detailed exception
      error_log($exception->getMessage());
    }

    return 0;
  }

  /**
   * Find the user for password reset, by the specified token and check the token hasn't expired
   *
   * @param string $token  Reset token
   * @return mixed         User object if found and the token hasn't expired, null otherwise
   */
  public static function findForPasswordReset($token)
  {
    $hashed_token = sha1($token);

    try {

      $db = Database::getInstance();

      $stmt = $db->prepare('SELECT * FROM users WHERE password_reset_token = :token LIMIT 1');
      $stmt->execute([':token' => $hashed_token]);
      $user = $stmt->fetchObject('User');

      if ($user !== false) {

        // Check the token hasn't expired
        $expiry = DateTime::createFromFormat('Y-m-d H:i:s', $user->password_reset_expires_at);

        if ($expiry !== false) {
          if ($expiry->getTimestamp() > time()) {
            return $user;
          }
        }
      }

    } catch(PDOException $exception) {

      error_log($exception->getMessage());
    }
  }

  /**
   * Activate the user account, nullifying the activation token and setting the is_active flag
   *
   * @param string $token  Activation token
   * @return void
   */
  public static function activateAccount($token)
  {
    $hashed_token = sha1($token);

    try {

      $db = Database::getInstance();

      $stmt = $db->prepare('UPDATE users SET activation_token = NULL, is_active = TRUE WHERE activation_token = :token');
      $stmt->execute([':token' => $hashed_token]);

    } catch(PDOException $exception) {

      // Log the detailed exception
      error_log($exception->getMessage());
    }
  }

  /**
   * Remember the login by storing a unique token associated with the user ID
   *
   * @param integer $expiry  Expiry timestamp
   * @return mixed           The token if remembered successfully, false otherwise
   */
  public function rememberLogin($expiry)
  {

    // Generate a unique token
    $token = uniqid($this->email, true);

    try {

      $db = Database::getInstance();

      $stmt = $db->prepare('INSERT INTO remembered_logins (token, user_id, expires_at) VALUES (:token, :user_id, :expires_at)');
      $stmt->bindParam(':token', sha1($token));  // store a hash of the token
      $stmt->bindParam(':user_id', $this->id, PDO::PARAM_INT);
      $stmt->bindParam(':expires_at', date('Y-m-d H:i:s', $expiry));
      $stmt->execute();

      if ($stmt->rowCount() == 1) {
        return $token;
      }

    } catch(PDOException $exception) {

      // Log the detailed exception
      error_log($exception->getMessage());
    }

    return false;
  }

  /**
   * Forget the login based on the token value
   *
   * @param string $token  Remember token
   * @return void
   */
  public function forgetLogin($token)
  {
    if ($token !== null) {

      try {

        $db = Database::getInstance();

        $stmt = $db->prepare('DELETE FROM remembered_logins WHERE token = :token');
        $stmt->bindParam(':token', $token);
        $stmt->execute();

      } catch(PDOException $exception) {

        // Log the detailed exception
        error_log($exception->getMessage());
      }
    }
  }

  /**
   * Start the password reset process by generating a unique token and expiry and saving them in the user model
   *
   * @return boolean  True if the user model was updated successfully, false otherwise
   */
  public function startPasswordReset()
  {
    // Generate a random token and base64 encode it so it's URL safe
    $token = base64_encode(uniqid(rand(), true));
    $hashed_token = sha1($token);

    // Set the token to expire in one hour
    $expires_at = date('Y-m-d H:i:s', time() + 60 * 60);

    try {

      $db = Database::getInstance();

      $stmt = $db->prepare('UPDATE users SET password_reset_token = :token, password_reset_expires_at = :expires_at WHERE id = :id');
      $stmt->bindParam(':token', $hashed_token);
      $stmt->bindParam(':expires_at', $expires_at);
      $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
      $stmt->execute();

      if ($stmt->rowCount() == 1) {
        $this->password_reset_token = $token;
        $this->password_reset_expires_at = $expires_at;

        return true;
      }

    } catch(PDOException $exception) {

     // Log the detailed exception
     error_log($exception->getMessage());
    }

    return false;
  }


  /**
   * Reset the password
   *
   * @return boolean  true if the password was changed successfully, false otherwise
   */
  public function resetPassword()
  {
    $password_error = $this->_validatePassword();

    if ($password_error === null) {

      try {

        $db = Database::getInstance();

        $stmt = $db->prepare('UPDATE users SET password = :password, password_reset_token = NULL, password_reset_expires_at = NULL WHERE id = :id');
        $stmt->bindParam(':password', Hash::make($this->password));
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
          return true;
        }

      } catch(PDOException $exception) {

        // Set generic error message and log the detailed exception
        $this->errors = ['error' => 'A database error occurred.'];
        error_log($exception->getMessage());
      }

    } else {
      $this->errors['password'] = $password_error;
    }

    return false;
  }

  /**
   * Validate the properties and set $this->errors if any are invalid
   *
   * @return boolean  true if valid, false otherwise
   */
  public function isValid()
  {
    $this->errors = [];

    //
    // name
    //
    if ($this->name == '') {
      $this->errors['name'] = 'Please enter a valid name';
    }

    //
    // email address
    //
    if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
      $this->errors['email'] = 'Please enter a valid email address';
    }

    if ($this->findByEmail($this->email)) {
      $this->errors['email'] = 'That email address is already taken';
    }

    //
    // password
    //
    $password_error = $this->_validatePassword();
    if ($password_error !== null) {
      $this->errors['password'] = $password_error;
    }

    return empty($this->errors);
  }

  /**
   * Validate the password
   *
   * @return mixed  The first error message if invalid, null otherwise
   */
  private function _validatePassword()
  {
    if (strlen($this->password) < 5) {
      return 'Please enter a longer password';
    }

    if (isset($this->password_confirmation) && ($this->password != $this->password_confirmation)) {
      return 'Please enter the same password';
    }
  }

  /**
   * Send activation email to the user based on the token
   *
   * @param string $token  Activation token
   * @return mixed         User object if authenticated correctly, null otherwise
   */
  private function _sendActivationEmail($token)
  {
    // Note hardcoded protocol
    $url = 'http://'.$_SERVER['HTTP_HOST']. Util::SERVER_HOST . '/pages/activate_account.php?token=' . $token;

    $body = <<<EOT

<p>Please click on the following link to activate your account.</p>
<p><a href="$url">$url</a></p>
EOT;

    Mail::send($this->name, $this->email, 'Activate account', $body);
  }

}
