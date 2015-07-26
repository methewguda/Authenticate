<?php

/**
 * Reset password success page
 */

// Initialisation
require_once('../includes/init.php');

// Set the title, show the page header, then the rest of the HTML
$page_title = 'Reset password';
include('../includes/header.php');

?>

<div class="container">
  <div class="success">
    <h2 class="success-heading">Reset Password</h2>

    <p class="success-messege">
      Success! Your password has been reset.<br>
      You can now <a href="../bazinga/pages/signin.php">Sign In</a>.
    </p>

  </div>
</div>

<?php include('../includes/footer.php'); ?>
