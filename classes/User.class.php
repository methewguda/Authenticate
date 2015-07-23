<?php

/**
 * User class
 */

class User
{

  /**
   * Signup a new user
   *
   * @param array $data  POST data
   * @return void
   */
  public static function signup($data)
  {
    try {

      $db = Database::getInstance();

      $stmt = $db->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');
      $stmt->bindParam(':name', $data['name']);
      $stmt->bindParam(':email', $data['inputEmail']);
      $stmt->bindParam(':password', Hash::make($data['inputPassword']));
      $stmt->execute();

    } catch(PDOException $exception) {

      // Log the exception message
      error_log($exception->getMessage());
    }
  }

}
