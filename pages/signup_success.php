<?php

/**
 * Signup success page
 */

// Initialisation
require_once('../includes/init.php');

// Set the title, show the page header, then the rest of the HTML
$page_title = 'Success';
include('../includes/header.php');

?>

<div class="container">
  <div class="success">
    <h1 class="success-heading">Sign Up Success!</h1>

    <p class="success-messege">
      Success! Thank you for signing up.<br>
      Please check your email for an account activation message.</a>
    </p>

  </div>
</div>

<?php include('../includes/footer.php'); ?>
