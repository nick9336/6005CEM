<!--security added
1.csrf_token
2.session 
3.validation

-->

<?php

include 'components/connect.php';

session_start();
if (empty($_SESSION['csrf_token'])) {
   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
};

if(isset($_POST['submit'])){
   if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
       die('CSRF token validation failed');
   }

   $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
   $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
   $number = filter_input(INPUT_POST, 'number', FILTER_SANITIZE_STRING);
   $old_pass = filter_input(INPUT_POST, 'old_pass', FILTER_SANITIZE_STRING);
   $new_pass = filter_input(INPUT_POST, 'new_pass', FILTER_SANITIZE_STRING);
   $confirm_pass = filter_input(INPUT_POST, 'confirm_pass', FILTER_SANITIZE_STRING);

   if(!empty($name)){
      if(!preg_match("/^[a-zA-Z ]*$/", $name) || strlen($name) < 3){
         $message[] = 'Please enter a valid name with minimum 3 letters.';
      } else {
         $check_name = $conn->prepare("SELECT * FROM `users` WHERE name = ? AND id != ?");
         $check_name->execute([$name, $user_id]);
         if($check_name->rowCount() > 0){
            $message[] = 'Exist Name!';
         } else {
            $update_name = $conn->prepare("UPDATE `users` SET name = ? WHERE id = ?");
            $update_name->execute([$name, $user_id]);
            $message[] = 'Name Updated';
         }
      }
   }

   if(!empty($email)){
      if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
         $message[] = 'Invalid email format.';
      } else {
         $select_email = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND id != ?");
         $select_email->execute([$email, $user_id]);
         if($select_email->rowCount() > 0){
            $message[] = 'Email already taken!';
         } else {
            $update_email = $conn->prepare("UPDATE `users` SET email = ? WHERE id = ?");
            $update_email->execute([$email, $user_id]);
            $message[] = 'Email updated successfully!';
         }
      }
   }

   // Validate contact number
   if(!empty($number)){
      if(!preg_match("/^\d{10,11}$/", $number)){
         $message[] = 'Please enter a valid contact number.';
      } else {
         $select_number = $conn->prepare("SELECT * FROM `users` WHERE number = ? AND id != ?");
         $select_number->execute([$number, $user_id]);
         if($select_number->rowCount() > 0){
            $message[] = 'Contact number already taken!';
         } else {
            $update_number = $conn->prepare("UPDATE `users` SET number = ? WHERE id = ?");
            $update_number->execute([$number, $user_id]);
            $message[] = 'Contact updated successfully!';
         }
      }
   }
   
   if(!empty($old_pass) && !empty($new_pass) && !empty($confirm_pass)){
      $select_prev_pass = $conn->prepare("SELECT password FROM `users` WHERE id = ?");
      $select_prev_pass->execute([$user_id]);
      $fetch_prev_pass = $select_prev_pass->fetch(PDO::FETCH_ASSOC);
      $prev_pass = $fetch_prev_pass['password'];

      if($old_pass != $prev_pass){
         $message[] = 'Old password not matched!';
      } else if($new_pass != $confirm_pass) {
         $message[] = 'Confirm password not matched!';
      } else if(!preg_match("/^(?=.*[A-Z])(?=.*[!@#$&*]).+$/", $new_pass)) {
         $message[] = 'Password must contain at least 1 uppercase letter and 1 special character.';
      } else {
         $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
         $update_pass->execute([$new_pass, $user_id]);
         $message[] = 'Password updated successfully!';
      }
   }  
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>update profile</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<section class="form-container update-form">

   <form action="" method="post">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
      <h3>update profile</h3>
      <input type="text" id="username" name="name" placeholder="<?= $fetch_profile['name']; ?>" class="box" maxlength="50">
      <span class="error-message" style="color:red;float:left;" id="name_error"></span>

      <script>
         document.getElementById('username').addEventListener('input', function(event) {
            var userName = this.value;
            var errorMessage = document.getElementById('name_error');

            var letters = /^[A-Za-z]+$/;

            this.value = this.value.replace(/[^A-Za-z]/g, '');

            if (userName.length < 3 || !letters.test(userName)) {
               errorMessage.textContent = 'Please enter a valid name | minimum 3 letters |';
            } else {
               errorMessage.textContent = 'Valid Name';
            }
         });
      </script>

      <input type="email" id="user_email" name="email" placeholder="<?= $fetch_profile['email']; ?>" class="box" maxlength="50"  oninput="validateEmail()" oninput="this.value = this.value.replace(/\s/g, '')">
      <span class="error-message" style="color:red; float:left;" id="email_error"></span>

      <script>
         function validateEmail() {
            var userEmail = document.getElementById('user_email').value;
            var errorMessage = document.getElementById('email_error');

            var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

            userEmail = userEmail.replace(/\s/g, '');

            if(emailPattern.test(userEmail)) {
                  errorMessage.innerHTML = 'Valid Email';
                  return true;
            } else {
                  errorMessage.innerHTML = 'Please enter a valid email address';

                  return false;
            }
         }
      </script>
      
      
      <input type="text" id="user_contact_number" name="number" placeholder="<?= $fetch_profile['number']; ?>" class="box" minlength="10" maxlength="11" oninput="validateContact()">
      <span class="error-message" style="color:red; float:left;" id="contact_error"></span>

      <script>
         function validateContact() {
            var userContact = document.getElementById('user_contact_number');
            
            // Remove non-numeric characters
            userContact.value = userContact.value.replace(/[^0-9]/g, '');

            var errorMessage = document.getElementById('contact_error');

            // Regular expression for validating phone number (starting with 01 and followed by 8 or 9 digits)
            var phonePattern = /^01[0-9]{8,9}$/;

            if(phonePattern.test(userContact.value)) {
                  errorMessage.innerHTML = 'Valid Phone Number';
                  return true;
            } else {
                  errorMessage.innerHTML = 'Please enter a valid phone number';
                  return false;
            }
         }
      </script>
      
      <input type="password" id="old_password" name="old_pass" placeholder="enter your old password" class="box" maxlength="50" oninput="validateoldpass()" oninput="this.value = this.value.replace(/\s/g, '')">
      <span class="error-message" style="color:red;float:left;" id="old_password_error"></span>

      <script>
         function validateoldpass() {
            var oldpassword = document.getElementById('old_password').value;
            var oldpasswordError = document.getElementById('old_password_error');

            // Clear any previous error messages
            oldpasswordError.innerHTML = '';

            var oldpasswordPattern = /^(?=.*[A-Z])(?=.*[!@#$&*]).+$/;

            if (!oldpasswordPattern.test(oldpassword)) {
                  oldpasswordError.innerHTML = 'Must contain at least 1 uppercase letter and 1 special character';
                  return false;
            }

            return true;
         }
      </script>



      <input type="password" id="user_password" name="new_pass" placeholder="enter your new password" class="box" maxlength="50" oninput="validatePassword()" oninput="this.value = this.value.replace(/\s/g, '')">
      <span class="error-message" style="color:red;float:left;" id="password_error"></span>



      <input type="password" id="user_confirm_password" name="confirm_pass" placeholder="confirm your new password" class="box" maxlength="50" oninput="validatePassword()" oninput="this.value = this.value.replace(/\s/g, '')">
      <span class="error-message" style="color:red;float:left;" id="confirm_password_error"></span>

      <script>
         

         function validatePassword() {
            var password = document.getElementById('user_password').value;
            var confirmPassword = document.getElementById('user_confirm_password').value;
            var passwordError = document.getElementById('password_error');
            var confirmPasswordError = document.getElementById('confirm_password_error');

            var passwordPattern = /^(?=.*[A-Z])(?=.*[!@#$&*]).+$/;

            passwordError.innerHTML = '';
            confirmPasswordError.innerHTML = '';

            if (!passwordPattern.test(password)) {
               passwordError.innerHTML = '1 uppercase letter and 1 special character';
               return false;
            }

            if (password !== confirmPassword) {
               confirmPasswordError.innerHTML = 'Passwords do not match';
               return false;
            }

            return true;
         }
      </script>
      <input type="submit" value="update now" name="submit" class="btn">
   </form>

</section>










<?php include 'components/footer.php'; ?>






<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>