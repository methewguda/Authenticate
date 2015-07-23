<?php

/**
 * Hash class
 */

class Hash
{

  private static $_hasher;

  private function __construct() {}  // disallow creating a new object of the class with new Hash()

  private function __clone() {}  // disallow cloning the class


  /**
   * Get a hash of the text
   *
   * @param string $text  The cleartext
   * @return string       The hash
   */
  public static function make($text)
  {
    return static::_getHasher()->HashPassword($text);
  }


  /**
   * Compare text to its hash
   *
   * @param string $text  The cleartext
   * @param string $hash  The hash
   * @return boolean
   */
  public static function check($text, $hash)
  {
    return static::_getHasher()->CheckPassword($text, $hash);
  }


  /**
   * Get the singleton password hasher object
   *
   * @return PasswordHash
   */
  private static function _getHasher()
  {
    if (static::$_hasher === NULL) {

      require dirname(dirname(__FILE__)) . '/libraries/PHPass/PasswordHash.php';

      static::$_hasher = new PasswordHash(8, false);
    }

    return static::$_hasher;
  }

}
