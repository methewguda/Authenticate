<?php

/**
 * Activate new account
 */

// Initialisation
require_once('../includes/init.php');


// Activate the account for the user with the token
if (isset($_GET['token'])) {
  User::activateAccount($_GET['token']);
}


// Set the title, show the page header, then the rest of the HTML
$page_title = 'Activate account';
include('../includes/header.php');

?>

<div class="container">
  <div class="success">
    <h1 class="success-heading">Account Activated!</h1>

    <p class="success-messege">
      Thank you for activating your account!<br>
      You can now <a href="../bazinga/pages/signin.php">Sign In</a>.
    </p>

  </div>
</div>

<?php include('../includes/footer.php'); ?>
