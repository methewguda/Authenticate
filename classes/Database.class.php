<?php

/**
 * Database class
 */

class Database
{

  private static $_db;  // singleton connection object

  private function __construct() {}  // disallow creating a new object of the class with new Database()

  private function __clone() {}  // disallow cloning the class

  /**
   * Get the instance of the PDO connection
   *
   * @return DB  PDO connection
   */
  public static function getInstance()
  {
    if (static::$_db === NULL)
    {
      $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8';
      static::$_db = new PDO($dsn, Config::DB_USER, Config::DB_PASS);

      // Raise exceptions when a database exception occurs
      static::$_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    return static::$_db;
  }

}
