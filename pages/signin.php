<?php

/**
 * Sign In an existing User
 */

 // Initialisation
 require_once('../includes/init.php');

 // Require the user to NOT be logged in before they can see this page.
 Auth::getInstance()->requireGuest();

 // Get checked status of the "remember me" checkbox
 $remember_me = isset($_POST['remember_me']);

 // Process the submitted form
 if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   $email = $_POST['inputEmail'];

   if (Auth::getInstance()->login($email, $_POST['inputPassword'], $remember_me)) {

    // Redirect to home page or intended page, if set
    if (isset($_SESSION['return_to'])) {
      $url = $_SESSION['return_to'];
      unset($_SESSION['return_to']);
    } else {
      $url = '/index.php';
    }

    Util::redirect($url);
  }
 }

 // Set the title, show the page header, then the rest of the HTML
 $page_title = 'Sign In';
 include('../includes/header.php');

?>

<div class="error-log">
 <?php if (isset($email)): ?>
  <p>Invalid login</p>
 <?php endif; ?>
</div>

<div class="container">
  <form class="form-signin" method="POST">
    <h2 class="form-signin-heading">Please Sign In Here</h2>
    <label for="inputEmail" class="sr-only">Email Address</label>
    <input type="email" id="inputEmail" name="inputEmail" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
           class="form-control" placeholder="Email Address" required autofocus>
    <label for="inputPassword" class="sr-only">Password</label>
    <input type="password" id="inputPassword" name="inputPassword"
           class="form-control" placeholder="Password" required>
    <div class="checkbox">
      <label>
        <input id="remember_me" name="remember_me" type="checkbox" value="1">
        <?php if ($remember_me): ?>checked="checked"<?php endif; ?>Remember me
      </label>
      <a class="forgotPassword" href="pages/forgot_password.php">I forgot my password</a>
    </div>

    <button class="btn btn-lg btn-primary btn-block" type="submit" value="Sign In">Sign In</button>
  </form>
</div> <!-- /container -->

<?php include('../includes/footer.php'); ?>
