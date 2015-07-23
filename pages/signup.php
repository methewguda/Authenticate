<?php

/**
 * Sign Up a New User
 */

// Set the title, show the page header, then the rest of the HTML
$page_title = 'Sign Up';
include('../includes/header.php');
?>

<div class="container">
  <form id="myform" class="form-signin" method="POST">
    <h2 class="form-signin-heading">Please Sign Up</h2>
    <label for="Name" class="sr-only">Name</label>
    <input type="text" id="name" name="name" value=""
           class="form-control" placeholder="Name" required autofocus>
    <label for="inputEmail" class="sr-only">Email address</label>
    <input type="email" id="inputEmail" name="inputEmail" value=""
           class="form-control" placeholder="Email Address" required>
    <label for="inputPassword" class="sr-only">Password</label>
    <input type="password" id="inputPassword" name="inputPassword" class="form-control" placeholder="Password" required>
    <label for="confirmPassword" class="sr-only">Confirm Password</label>
    <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Confirm Password" required>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Sign Up</button>
  </form>
</div> <!-- /container -->

<?php include('../includes/footer.php'); ?>
