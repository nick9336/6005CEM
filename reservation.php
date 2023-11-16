<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
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
         } else {
            if (isset($_POST['submit'])) {
               $user_query = $conn->prepare("SELECT name FROM users WHERE id = ?");
               $user_query->execute([$user_id]);
               $user = $user_query->fetch(PDO::FETCH_ASSOC);
               $username = $user['name'];

               $email = isset($_POST['email']) ? $_POST['email'] : '';
               $place_on = isset($_POST['place_on']) ? $_POST['place_on'] : '';
               $time_place = isset($_POST['time_place']) ? $_POST['time_place'] : '';
               $pax = isset($_POST['pax']) ? $_POST['pax'] : '';
               $contact_no = isset($_POST['contact_no']) ? $_POST['contact_no'] : '';
               

               if (!empty($username) && !empty($email) && !empty($place_on) && !empty($time_place) && !empty($pax) && !empty($contact_no)) {
                  $insert_res = $conn->prepare("INSERT INTO reservations (user_id, name, placed_on, time_placed, pax, contact_no, email, reservation_status)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                  $insert_res->execute([$user_id, $username, $place_on, $time_place, $pax, $contact_no, $email, 'Pending']);
                  $message[] = 'Reservation Made!';

                  $mail = new PHPMailer();
                  $mail->isSMTP();
                  $mail->Host = 'smtp.gmail.com';
                  $mail->SMTPAuth = true;
                  $mail->Username = 'jackleeleow@gmail.com';
                  $mail->Password = 'piwmdkmynleqejvn';
                  $mail->SMTPSecure = 'ssl';
                  $mail->Port = 465;
                  $mail->setFrom('jackleeleow@gmail.com');
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
                  $mail->send();
               }
            } else {
               echo '
                  <div class="box">
                     <h2>Reservation Form</h2>
                     <form action="reservation.php" method="POST">
                        <div class="form-group">
                           <p>Reservation Date: 
                           <input type="date" id="place_on" name="place_on" required></p>
                        </div>

                        <div class="form-group">
                           <p>Time of Reservation:
                           <input type="time" id="time_place" name="time_place" required></p>
                        </div>

                        <div class="form-group">
                           <p>Email:
                           <input type="email" id="email" name="email" required></p>
                        </div>

                        <div class="form-group">
                           <p>Contact No:
                           <input type="text" id="contact_no" name="contact_no" required></p>
                        </div>

                        <div class="form-group">
                           <p>Number of Pax: 
                           <input type="text" id="pax" name="pax" required></p>
                        </div>
                        
                        <center>
                           <input type="submit" value="Submit Reservation" id="submit" name="submit">
                        </center>
                     </form>
                  </div>
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
