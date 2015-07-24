<?php

/**
 * Sign In an existing User
 */

 // Initialisation
 require_once('../includes/init.php');

 // Process the submitted form
 if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   $email = $_POST['inputEmail'];

   if (Auth::getInstance()->login($email, $_POST['inputPassword'])) {
     // Redirect to home page
     Util::redirect('/index.php');
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
        <input id="remember_me" name="remember_me" type="checkbox" value="1"> Remember me
      </label>
      <a class="forgotPassword" href="pages/forgot_password.php">I forgot my password</a>
    </div>

    <button class="btn btn-lg btn-primary btn-block" type="submit" value="Login">Sign in</button>
  </form>
</div> <!-- /container -->

<?php include('../includes/footer.php'); ?>
