<?php

/**
 * Cleanup expired remember tokens
 */

// Initialisation
require_once('../includes/init.php');

echo User::deleteExpiredTokens();

?>
