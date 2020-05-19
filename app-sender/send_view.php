<?php

//echo phpinfo();die;
session_start([
    'cookie_lifetime' => 86400,
]);

if (!isset($_SESSION['email']) && !isset($_POST['email'])) {
    header('Location: http://localhost:2222/login.php');
    exit;
}

 if (isset($_POST['email'])) {
     $_SESSION['email'] = $_POST['email'];
 }

?>

<!DOCTYPE HTML>
<html>
 <head>
     <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
     <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
     <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <meta charset="utf-8">
  <title>Send message</title>
 </head>
 <body>
 <div id="login">
     <h3 class="text-center text-white pt-5">Send Message</h3>
     <div class="container">
         <div id="login-row" class="row justify-content-center align-items-center">
             <div id="login-column" class="col-md-6">
                 <div id="login-box" class="col-md-12">
                     <form id="login-form" class="form" action="send.php" method="post">
                         <h3 class="text-center text-info">Message</h3>
                         <div class="form-group">
                             <label for="message" class="text-info">Message</label><br>
                             <input type="text" name="message" id="message" class="form-control">
                         </div>
                         <div class="form-group">
                             <label for="email" class="text-info">email</label><br>
                             <input type="email" name="email" id="email" value="<?php echo $_SESSION['email'] ?>" class="form-control">
                         </div>
                         <div class="form-group">
                             <input type="submit" name="submit" class="btn btn-info btn-md" value="send">
                         </div>
                     </form>
                 </div>
             </div>
         </div>
     </div>
 </div>

 </body>
</html>