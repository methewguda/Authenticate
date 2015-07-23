<?php

 /**
  * Homepage
  */

 // Initialisation
 require_once('includes/init.php');

 // Set the title, show the page header, then the rest of the HTML
 $page_title = 'Home';
 include('includes/header.php');
 
?>

<main id="homepage">
  <div class="container">
    <form class="form-signin">
        <h2 class="form-signin-heading">Bazinga Authentication</h2>
        <a href="pages/signup.php"
           class="btn btn-lg btn-primary btn-block">Sign Up</a>
        <a href="pages/signin.php"
           class="btn btn-lg btn-primary btn-block">Sign In</a>
      </form>
  </div>
</main>

<?php

// Show the page footer at the end of the page.
include('includes/footer.php');
?>
