<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
};

if (!isset($_SESSION['csrf_token'])) {
   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['submit'])) {

   if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
      // Invalid CSRF token, handle accordingly
      exit('Invalid CSRF token');
  }

   $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
   $pass = $_POST['pass'];
   $cpass = $_POST['cpass'];

   // Password Policy
   $min_password_length = 8;
   $require_uppercase = true;
   $require_lowercase = true;
   $require_numbers = true;
   $require_special_characters = true;

   // Validate Password
   $is_valid_password = validatePassword($pass, $min_password_length, $require_uppercase, $require_lowercase, $require_numbers, $require_special_characters);

   if (!$is_valid_password) {
       $message[] = 'Password does not meet the requirements.';
   } else {
      $select_rest = $conn->prepare("SELECT * FROM `rest` WHERE name = ?");
      $select_rest->execute([$name]);
   
      if ($select_rest->rowCount() > 0) {
          $message[] = 'Username already exists!';
      } else {
          if ($pass != $cpass) {
              $message[] = 'Password not matched!';
          } else {
              // Hash the password before storing it
              $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
              $insert_rest = $conn->prepare("INSERT INTO `rest`(name, password) VALUES(?,?)");
              $insert_rest->execute([$name, $hashed_password]);
              $message[] = 'New employee registered!';
         }
      }
   }

   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function validatePassword($password, $min_length, $require_uppercase, $require_lowercase, $require_numbers, $require_special_characters)
{
   $length = strlen($password);
   if ($length < $min_length) {
       return false;
   }

   if ($require_uppercase && !preg_match('/[A-Z]/', $password)) {
       return false;
   }

   if ($require_lowercase && !preg_match('/[a-z]/', $password)) {
       return false;
   }

   if ($require_numbers && !preg_match('/[0-9]/', $password)) {
       return false;
   }

   if ($require_special_characters && !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
       return false;
   }

   return true;
}  


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/rest_style.css">

</head>
<body>

<?php include '../components/header.php' ?>

<!-- register rest section starts  -->

<section class="form-container">

   <form action="" method="POST">
   <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

      <h3>register new</h3>
      <input type="text" name="name" maxlength="20" required placeholder="Enter username" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" maxlength="20" required placeholder="Enter password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" maxlength="20" required placeholder="Confirm password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="register now" name="submit" class="btn">
   </form>

</section>

<!-- register rest section ends -->
















<!-- custom js file link  -->
<script src="../js/rest_script.js"></script>

</body>
</html>