<?php

include 'components/connect.php';

function generateCSRFToken() {
   $csrf_token = bin2hex(random_bytes(32));
   $_SESSION['csrf_token'] = $csrf_token;
}

session_start();

// Generate and store a new CSRF token in the session if it doesn't exist
if (!isset($_SESSION['csrf_token'])) {
   generateCSRFToken();
}

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}

if(isset($_POST['send'])){
   
   // Validate CSRF token
if (isset($_POST['csrf_token']) && hash_equals($_POST['csrf_token'], $_SESSION['csrf_token'])) {
   
      $name = $_POST['name'];
      $name = filter_var($name, FILTER_SANITIZE_STRING);
      $email = $_POST['email'];
      $email = filter_var($email, FILTER_SANITIZE_STRING);
      $number = $_POST['number'];
      $number = filter_var($number, FILTER_SANITIZE_STRING);
      $msg = $_POST['msg'];
      $msg = filter_var($msg, FILTER_SANITIZE_STRING);
   
      $select_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
      $select_message->execute([$name, $email, $number, $msg]);
   
      if($select_message->rowCount() > 0){
         $message[] = 'Already sent message!';
      }else{
   
         $insert_message = $conn->prepare("INSERT INTO `messages`(user_id, name, email, number, message) VALUES(?,?,?,?,?)");
         $insert_message->execute([$user_id, $name, $email, $number, $msg]);
   
         $message[] = 'Sent message successfully!';
   
      }
   
   } else {
      $message[] = 'CSRF token validation failed!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>contact</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<div class="heading">
   <h3>contact us</h3>
   <p><a href="home.php">home</a> <span> / contact</span></p>
</div>

<!-- contact section starts  -->

<section class="contact">

   <div class="row">

      <div class="image">
         <img src="images/contact-img.svg" alt="">
      </div>

      <form action="" method="post">
         <h3>tell us something!</h3>
         <!-- Adding CSRF token input field -->
         <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
         <input type="text" name="name" maxlength="50" class="box" placeholder="enter your name" required>
         <input type="number" name="number" min="0" max="9999999999" class="box" placeholder="enter your number" required maxlength="10">
         <input type="email" name="email" maxlength="50" class="box" placeholder="enter your email" required>
         <textarea name="msg" class="box" required placeholder="enter your message" maxlength="500" cols="30" rows="10"></textarea>
         <input type="submit" value="send message" name="send" class="btn">
      </form>

   </div>

</section>

<!-- contact section ends -->

<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>