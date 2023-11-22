<?php

include '../components/connect.php';

session_start();

$rest_id = $_SESSION['rest_id'];

if (!isset($rest_id)) {
    header('location:rest_login.php');
    exit; // Ensure script stops execution after redirect
}

if (!isset($_SESSION['csrf_token'])) {
   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['submit'])) {

   if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
      // Invalid CSRF token, handle accordingly
      exit('Invalid CSRF token');
  } 

    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);

    if (!empty($name)) {
        $select_name = $conn->prepare("SELECT * FROM `rest` WHERE name = ?");
        $select_name->execute([$name]);

        if ($select_name->rowCount() > 0) {
            $message[] = 'Username already exists!';
        } else {
            $update_name = $conn->prepare("UPDATE `rest` SET name = ? WHERE id = ?");
            $update_name->execute([$name, $rest_id]);
            $message[] = 'Username updated successfully!';
        }
    }

    $old_pass = filter_input(INPUT_POST, 'old_pass', FILTER_SANITIZE_STRING);
    $new_pass = filter_input(INPUT_POST, 'new_pass', FILTER_SANITIZE_STRING);
    $confirm_pass = filter_input(INPUT_POST, 'confirm_pass', FILTER_SANITIZE_STRING);

    if (!empty($old_pass)) {
      $select_old_pass = $conn->prepare("SELECT password FROM `rest` WHERE id = ?");
      $select_old_pass->execute([$rest_id]);
      $fetch_prev_pass = $select_old_pass->fetch(PDO::FETCH_ASSOC);
      $prev_pass = $fetch_prev_pass['password'];
  
      if (password_verify($old_pass, $prev_pass)) {
          if ($new_pass == $confirm_pass) {
              if (!empty($new_pass)) {
                  // Password Policy
                  $min_password_length = 8;
                  $require_uppercase = true;
                  $require_lowercase = true;
                  $require_numbers = true;
                  $require_special_characters = true;
  
                  // Validate Password
                  $is_valid_password = validatePassword($new_pass, $min_password_length, $require_uppercase, $require_lowercase, $require_numbers, $require_special_characters);
  
                  if ($is_valid_password) {
                      $hashed_password = password_hash($new_pass, PASSWORD_BCRYPT);
                      $update_pass = $conn->prepare("UPDATE `rest` SET password = ? WHERE id = ?");
                      $update_pass->execute([$hashed_password, $rest_id]);
                      $message[] = 'Password updated successfully!';
                  } else {
                      $message[] = 'Password does not meet the requirements.';
                  }
              } else {
                  $message[] = 'Please enter a new password!';
              }
          } else {
              $message[] = 'Confirm password not matched!';
          }
      } else {
          $message[] = 'Old password not matched!';
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
<!-- The rest of your HTML code remains unchanged -->


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>profile update</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/rest_style.css">

</head>
<body>

<?php include '../components/rest_header.php' ?>

<!-- rest profile update section starts  -->

<section class="form-container">

   <form action="" method="POST">
   <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

      <h3>update profile</h3>
      <input type="text" name="name" maxlength="20" class="box" placeholder="Enter new username" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="old_pass" maxlength="20" placeholder="Enter old password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="new_pass" maxlength="20" placeholder="Enter new password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="confirm_pass" maxlength="20" placeholder="Confirm new password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="update now" name="submit" class="btn">
   </form>

</section>

<!-- rest profile update section ends -->









<!-- custom js file link  -->
<script src="../js/rest_script.js"></script>

</body>
</html>