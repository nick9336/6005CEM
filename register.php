<!--security added
1.csrf_token
2.session 
3.validation

-->
<?php

include 'components/connect.php';

session_start();

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['submit'])){if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF token validation failed');
}


   $message = []; // Array to store error messages

   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
   $pass = $_POST['pass']; // Password will be hashed, so no need to sanitize
   $cpass = $_POST['cpass'];

   // Validate empty fields
   if(empty($name) || empty($email) || empty($number) || empty($pass) || empty($cpass)){
      $message[] = 'Please fill in all fields';
   }

   // Validate name length and character
   if(strlen($name) < 3 || !preg_match("/^[a-zA-Z\s]*$/", $name)){
      $message[] = 'Invalid name, must be at least 3 letters and only contain alphabets';
   }

   // Validate email format
   if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
      $message[] = 'Invalid email format';
   }

   // Validate phone number format
   if(!preg_match("/^01[0-9]{8,9}$/", $number)){
      $message[] = 'Invalid phone number format';
   }

   // Validate password complexity and hash it
   if(!preg_match("/^(?=.*[A-Z])(?=.*[!@#$&*]).+$/", $pass)){
      $message[] = 'Password must contain an uppercase letter and a special character';
   } else {
      $hashedPassword = password_hash($pass, PASSWORD_BCRYPT);
   }

   // Check if passwords match
   if($pass !== $cpass){
      $message[] = 'Passwords do not match';
   }

   // Proceed with database operations if there are no errors
   if(empty($message)){
      $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? OR number = ?");
      $select_user->execute([$email, $number]);

      if($select_user->rowCount() > 0){
         $message[] = 'email or number already exists!';
      } else {
         $insert_user = $conn->prepare("INSERT INTO `users`(name, email, number, password) VALUES(?,?,?,?)");
         $insert_user->execute([$name, $email, $number, $hashedPassword]);
         $_SESSION['user_id'] = $conn->lastInsertId();
         header('location:home.php');
         exit;
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
   <title>register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<section class="form-container">

   <form action="" method="post"><input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

      <h3>register now</h3>
      <input type="text" name="name" id="username" required placeholder="Enter Your Name" class="box" minlength="3" >
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



      <input type="email" name="email" id="user_email" required placeholder="Enter Your Email" class="box" maxlength="50" oninput="validateEmail()">
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





      <input type="text" name="number" id="user_contact_number" required placeholder="Enter Your Contact Number" class="box" minlength="10" maxlength="11" oninput="validateContact()">
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



      <input type="password" name="pass" id="user_password" required placeholder="Enter Your Password" class="box" maxlength="50" oninput="validatePassword()">
      <span class="error-message" style="color:red;float:left;" id="password_error"></span>

      <input type="password" name="cpass" id="user_confirm_password" required placeholder="Confirm Your Password" class="box" maxlength="50" oninput="validatePassword()">
      <span class="error-message" style="color:red;float:left;" id="confirm_password_error"></span>

      <script>
         

         // Function to validate password
         function validatePassword() {
            var password = document.getElementById('user_password').value;
            var confirmPassword = document.getElementById('user_confirm_password').value;
            var passwordError = document.getElementById('password_error');
            var confirmPasswordError = document.getElementById('confirm_password_error');

            // Regular expression for validating password (at least one uppercase letter and one special character)
            var passwordPattern = /^(?=.*[A-Z])(?=.*[!@#$&*]).+$/;

            // Reset error messages
            passwordError.innerHTML = '';
            confirmPasswordError.innerHTML = '';

            // Check password against the pattern
            if (!passwordPattern.test(password)) {
               passwordError.innerHTML = '1 uppercase letter and 1 special character';
               return false;
            }

            // Check if passwords match
            if (password !== confirmPassword) {
               confirmPasswordError.innerHTML = 'Passwords do not match';
               return false;
            }

            return true;
         }
      </script>


      <input type="submit" value="register now" name="submit" class="btn">
     


      <p>already have an account? <a href="login.php">login now</a></p>

      <input type="checkbox" id="agreeTerms" name="agreeTerms" required>
      
      <label for="agreeTerms" style="font-size: 15px;">I agree to the <a href="terms.php" style="font-size: 15px;text-decoration:underline;">Terms and Conditions</a></label>
      
   </form>

</section>





<?php include 'components/footer.php'; ?>







<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>