<?php

/**
 * Sign In an existing User
 */

// Set the title, show the page header, then the rest of the HTML
$page_title = 'Sign In';
include('../includes/header.php');

?>

<div class="container">
  <form class="form-signin" method="POST">
    <h2 class="form-signin-heading">Please Sign In Here</h2>
    <label for="inputEmail" class="sr-only">Email Address</label>
    <input type="email" id="inputEmail" name="inputEmail" value=""
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
