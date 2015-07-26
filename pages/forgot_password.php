<?php

/**
 * Forgotten password form
 */

// Initialisation
require_once('../includes/init.php');

// Require the user to NOT be logged in before they can see this page.
Auth::getInstance()->requireGuest();

// Process the submitted form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  Auth::getInstance()->sendPasswordReset($_POST['email']);
  $message_sent = true;
}


// Set the title, show the page header, then the rest of the HTML
$page_title = 'Forgotten Password';
include('../includes/header.php');

?>

<div class="container">
  <?php if (isset($message_sent)): ?>
    <div class="success">
      <p class="success-messege">
         If we found an account with that email address.
         we have sent password reset instructions to it.
         Please check your email.
      </p>
    </div>
  <?php else: ?>
  <form class="form-signin" method="POST">
    <h2 class="form-signin-heading">Forgotten Password</h2>
    <label for="email" class="sr-only">email address</label>
    <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
           class="form-control" placeholder="Email Address" required autofocus>
  <button class="btn btn-lg btn-primary btn-block"type="submit" value="Send password reset instructions" />Reset Password</button>
</form>
<?php endif; ?>
</div> <!-- /container -->

<?php include('../includes/footer.php'); ?>
