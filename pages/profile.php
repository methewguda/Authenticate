<?php

/**
 * Profile
 */

// Initialisation
require_once('../includes/init.php');

// Require the user to be logged in before they can see this page.
Auth::getInstance()->requireLogin();

// Set the title, show the page header, then the rest of the HTML
$page_title = 'Profile';
include('../includes/header.php');

?>

<div class="container">
  <div class="success">
    <?php $user = Auth::getInstance()->getCurrentUser(); ?>
    <h1 class="success-heading">Profile</h1>
    <div class="nav-bar">
      <a href="pages/edit_profile.php">Edit Profile</a>
      <a href="pages/signout.php">Sign Out</a>
    </div>
    <p class="profile-view">
      Name: <?php echo htmlspecialchars($user->name); ?><br>
      Email: <?php echo htmlspecialchars($user->email); ?><br>
      Phone: <?php echo htmlspecialchars($user->phone);?>
    </p>
  </div>
</div>


<?php include('../includes/footer.php'); ?>
