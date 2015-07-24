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

      try {

        $db = Database::getInstance();

        $stmt = $db->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');
        $stmt->bindParam(':name', $user->name);
        $stmt->bindParam(':email', $user->email);
        $stmt->bindParam(':password', Hash::make($user->password));
        $stmt->execute();

      } catch(PDOException $exception) {

        // Log the exception message
        error_log($exception->getMessage());
      }
    }

    return $user;
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
    if (strlen($this->password) < 5) {
      $this->errors['password'] = 'Please enter a password longer than 5 digit';
    }

    return empty($this->errors);
  }

}
