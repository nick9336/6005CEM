<!--security added
1.csrf_token
2.session 
3.htmlspecialchars
4.validation

-->

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

include 'components/connect.php';

session_start();

if (empty($_SESSION['csrf_token'])) {
   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['initiated'])) {
   session_regenerate_id();
   $_SESSION['initiated'] = true;
}

//session to check the user id 
if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Make Reservation</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <style>
      input[type=submit] {
         background: orange; 
         border: 2px solid orange;
         color: white;
         padding: 20px 50px 20px 50px;
      }

      input[type=submit]:hover {
         background: white; 
         color: orange;
         border: 2px solid orange;
         border-color: orange;
         padding: 20px 50px 20px 50px;
      }
   </style>
</head>
<body>
   <!-- header section starts  -->
   <?php include 'components/user_header.php'; ?>
   <!-- header section ends -->

   <div class="heading">
      <h3>Make Reservation</h3>
      <p><a href="home.php">home</a> <span> / Make Reservation</span></p>
   </div>

   <section class="reservation">
      <h1 class="title">Make Reservation</h1>

      <div class="box-container">
         <?php
         if ($user_id == '') {
            echo '<p class="empty">Please login to make a reservation</p>';
         } 
         else {
            if (isset($_POST['submit'])) {if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
               die('CSRF token validation failed');
           }
               $user_query = $conn->prepare("SELECT name FROM users WHERE id = ?");
               $user_query->execute([$user_id]);
               $user = $user_query->fetch(PDO::FETCH_ASSOC);
               $username = $user['name']; 
               
               $errors = []; 

               $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
               $place_on = filter_input(INPUT_POST, 'place_on', FILTER_SANITIZE_STRING);
               $time_place = filter_input(INPUT_POST, 'time_place', FILTER_SANITIZE_STRING);
               $pax = filter_input(INPUT_POST, 'pax', FILTER_SANITIZE_NUMBER_INT);
               $contact_no = filter_input(INPUT_POST, 'contact_no', FILTER_SANITIZE_NUMBER_INT);
   
               if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                  $errors['email'] = 'Invalid email address.';
               }
               if (empty($place_on)) { 
                  $errors['place_on'] = 'Reservation date is required.';
               }
               if (empty($time_place) || !preg_match("/^(0[9]|1[0-9]|2[0-3]):[0-5][0-9]$/", $time_place)) {
                  $errors['time_place'] = 'Time of reservation is required and must be between 09:00 and 23:59.';
               }
               if (empty($pax) || !filter_var($pax, FILTER_VALIDATE_INT) || $pax <= 0) {
                  $errors['pax'] = 'Number of pax must be a valid positive number.';
               }
               if (empty($contact_no) || !preg_match("/^\d{10,11}$/", $contact_no)) {
                  $errors['contact_no'] = 'Contact number is required and must be 10-11 digits.';
               }


                  if (!empty($username) && count($errors) === 0) {
                     $insert_res = $conn->prepare("INSERT INTO reservations (user_id, name, placed_on, time_placed, pax, contact_no, email, reservation_status)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                        $insert_res->execute([$user_id, $username, $place_on, $time_place, $pax, $contact_no, $email, 'Pending']);

                        $mail = new PHPMailer();
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'tengteng8132002@gmail.com';
                        $mail->Password = 'zzvmemdazozxzadq';
                        $mail->SMTPSecure = 'ssl';
                        $mail->Port = 465;
                        $mail->setFrom('tengteng8132002@gmail.com', 'Savoury Spoon');
                        $mail->addAddress($_POST["email"]);
                        $mail->isHTML(true);
                        $mail->Subject = 'The Savoury Spoon Reservation Made!';
                        $mail->Body = 'Dear ' . $username . ', <br/><br/>
                        Your Reservation Date: ' . $place_on . '<br/>
                        Your Time of Reservation: ' . $time_place . '<br/>
                        Your Email: ' . $email . '<br/>
                        Your Contact Number: ' . $contact_no . '<br/>
                        Number of Pax: ' . $pax . '<br/><br/>
                        Thank You for Your Reservation!';

                        if ($mail->send()){
                           header('location:history.php');
                       }
                     }
                      // Display errors if there are any
                     foreach ($errors as $error) {
                        echo "<p class='error' style='color: white;background-color:red;padding:10px;font-size:15px;margin-left:auto;margin-right:auto;'>$error</p>";
                     }
            }
             else {
               $today = date('Y-m-d');

               // Calculate the date 30 days from today
               $maxDate = date('Y-m-d', strtotime('+30 days'));
               echo '
                  <div class="box">
                     <h2>Reservation Form</h2>
                     <form action="reservation.php" method="POST">
                        <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">                        
                        <div class="form-group">
                           <p>Reservation Date: 
                           <input type="date" id="place_on" name="place_on" min="' . $today . '" max="' . $maxDate . '" required></p>
                           </div>


                        <div class="form-group">
                           <p>Time of Reservation:
                           <input type="time" id="time_place" name="time_place" required></p>
                           <span id="timeError"style="color:red; float:left;font-size:15px;"></span><br>
                        </div>

                        <div class="form-group">
                           <p>Email:
                           <input type="email" id="email" name="email" required oninput="validateEmail()"></p>
                           <span class="error-message" style="color:red; float:left;font-size:15px;" id="email_error"></span><br>
                           <script>
                              function validateEmail() {
                                 var userEmail = document.getElementById("email").value;
                                 var errorMessage = document.getElementById("email_error");

                                 var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

                                 userEmail = userEmail.replace(/\s/g, "");

                                 if(emailPattern.test(userEmail)) {
                                    errorMessage.innerHTML = "Valid Email";
                                    return true;
                                 } else {
                                    errorMessage.innerHTML = "Please enter a valid email address";
                                    return false;
                                 }
                              }
                           </script>
                        </div>

                        <div class="form-group">
                           <p>Contact No:
                           <input type="text" id="contact_no" name="contact_no" required minlength="10" maxlength="11" oninput="validateContact()"></p>
                           <span class="error-message" style="color:red; float:left;font-size:15px;" id="contact_error"></span><br>
                           <script>
                              function validateContact() {
                                 var userContact = document.getElementById("contact_no");
                                 
                                 userContact.value = userContact.value.replace(/[^0-9]/g, "");

                                 var errorMessage = document.getElementById("contact_error");

                                 var phonePattern = /^01[0-9]{8,9}$/;

                                 if(phonePattern.test(userContact.value)) {
                                       errorMessage.innerHTML = "Valid Phone Number";
                                       return true;
                                 } else {
                                       errorMessage.innerHTML = "Please enter a valid phone number";
                                       return false;
                                 }
                              }
                           </script>
                           
                        </div>

                        <div class="form-group">
                           <p>Number of Pax: 
                           <input type="text" id="pax" name="pax" required oninput="validateNumber()" maxlength="1000"></p>
                           <span class="error-message" style="color:red; float:left;font-size:15px;" id="numberpax_error"></span><br>
                           <script>
                              function validateNumber() {
                                 var userPax = document.getElementById("pax");
                                 var errorMessage = document.getElementById("numberpax_error");

                                 // Remove non-numeric characters
                                 userPax.value = userPax.value.replace(/[^0-9]/g, "");

                                 // Check if the input is a valid number
                                 if (userPax.value === "" || isNaN(userPax.value) || parseInt(userPax.value) <= 0) {
                                    errorMessage.innerHTML = "Please enter a valid number";
                                    userPax.value="";
                                    return false;
                                 } else {
                                    errorMessage.innerHTML = "Valid Number"; 
                                    return true;
                                 }
                              }
                           </script>
                        </div>
                        
                        <center>
                           <input type="submit" value="Submit Reservation" id="submit" name="submit">
                        </center>
                     </form>
                  </div>
                  <script> 
                        document.getElementById(\'time_place\').addEventListener(\'change\', function() {
                           var time = this.value;
                           var timeErrorSpan = document.getElementById(\'timeError\');
                           if (time >= "00:00" && time < "09:00") {
                              timeErrorSpan.textContent = \'Midnight reservations are not allowed.\';
                              this.value = \'\'; // Reset the value
                           } else {
                              timeErrorSpan.textContent = \'\'; // Clear the error message
                           }
                        });
                  
                  </script>


                  
               ';
            }
         }
         ?>
      </div>
   </section>

   <!-- footer section starts  -->
   <?php include 'components/footer.php'; ?>
   <!-- footer section ends -->

   <!-- custom js file link  -->
   <script src="js/script.js"></script>
</body>
</html>
