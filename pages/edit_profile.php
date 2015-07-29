<?php

/**
 * Sign In an existing User
 */

 // Initialisation
 require_once('../includes/init.php');

 // Require the user to be logged in before they can see this page.
 Auth::getInstance()->requireLogin();
 $user = Auth::getInstance()->getCurrentUser();

 // Process the submitted form
 if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   $user = User::updateProfile($_POST);

   if (empty($user->errors)) {

     // Redirect to signup success page
     Util::redirect('/pages/profile.php');
   }

 }

 // Set the title, show the page header, then the rest of the HTML
 $page_title = $user->name;
 include('../includes/header.php');

?>

<div class="error-log">
 <?php if (isset($email)): ?>
  <p>Invalid login</p>
 <?php endif; ?>
</div>

<div class="container">
  <form class="form-signin" method="POST">

    <h2 class="form-signin-heading">Welcome <?php echo htmlspecialchars($user->name); ?></h2>

    <label for="Name" class="sr-only">Name</label>
    <input type="text" id="name" name="name" value="<?php echo isset($user) ? htmlspecialchars($user->name) : ''; ?>"
           class="form-control" placeholder="Name" disabled>

    <label for="email" class="sr-only">Email Address</label>
    <input type="email" id="email" name="email" value="<?php echo isset($user) ? htmlspecialchars($user->email) : ''; ?>"
           class="form-control" placeholder="Email Address" disabled>

    <label for="phone" class="sr-only">Phone</label>
    <input type="tel" id="phone" name="phone" value="<?php echo isset($user) ? htmlspecialchars($user->phone) : ''; ?>"
           class="form-control" placeholder="Phone" required>

    <button class="btn btn-lg btn-primary btn-block" type="submit" value="Save">Save</button>
  </form>
</div> <!-- /container -->

<?php include('../includes/footer.php'); ?>
