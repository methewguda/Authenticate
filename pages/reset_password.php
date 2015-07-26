<?php

/**
 * Reset password form
 */

// Initialisation
require_once('../includes/init.php');

// Require the user to NOT be logged in before they can see this page.
Auth::getInstance()->requireGuest();


// Process the submitted form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $user = User::findForPasswordReset($_POST['token']);

  if ($user !== null) {

    $user->password = $_POST['password'];
    $user->password_confirmation = $_POST['password_confirmation'];

    if ($user->resetPassword()) {

      // Redirect to success page
      Util::redirect('/pages/reset_password_success.php');
    }
  }

} else {  // GET

  // Find the user based on the token
  if (isset($_GET['token'])) {
    $user = User::findForPasswordReset($_GET['token']);
  }
}


// Set the title, show the page header, then the rest of the HTML
$page_title = 'Reset password';
include('../includes/header.php');

?>

<?php if (isset($user)): ?>

  <?php if ( ! empty($user->errors)): ?>
    <ul>
      <?php foreach ($user->errors as $error): ?>
        <li><?php echo $error; ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
<div class="container">
  <form class="form-signin" method="POST">
    <input type="hidden" id="token" name="token" value="<?php echo $_GET['token']; ?>" />
    <h2 class="form-signin-heading">Reset Password</h2>
    <label for="password" class="sr-only">New Password</label>
    <input type="password" id="password" name="password"
           class="form-control" placeholder="New Password" required>

    <label for="password_confirmation" class="sr-only">Repeat New password</label>
    <input type="password" id="password_confirmation" name="password_confirmation"
           class="form-control" placeholder="Repeat New password" required>

    <button class="btn btn-lg btn-primary btn-block" type="submit" value="Reset Password" />Reset Password</button>
  </form>
<?php else: ?>
  <div class="success">
    <p class="success-messege">
       Reset token not found or expired.<br>
       Please <a href="../bazinga/pages/forgot_password.php">try resetting your password again</a>.
    </p>
  </div>
<?php endif; ?>
</div> <!-- /container -->


<?php include('../includes/footer.php'); ?>
