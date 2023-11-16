<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';

};

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Reservation History</title>

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
   <h3>Reservation History</h3>
   <p><a href="html.php">home</a> <span> / Reservation History</span></p>
</div>

<section class="history">

   <h1 class="title">Reservation History</h1>

   <div class="box-container">

   <?php
      if($user_id == ''){
         echo '<p class="empty">please login to see your orders</p>';
      }else{
         $select_reservations = $conn->prepare("SELECT * FROM `reservations` WHERE user_id = ?");
         $select_reservations->execute([$user_id]);
         if($select_reservations->rowCount() > 0){
            while($fetch_reservations = $select_reservations->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p>Reservation Date : <span><?= $fetch_reservations['placed_on']; ?></span></p>
      <p>Time of Reservation : <span><?= $fetch_reservations['time_placed']; ?></span></p>
      <p>Name : <span><?= $fetch_reservations['name']; ?></span></p>
      <p>Email : <span><?= $fetch_reservations['email']; ?></span></p>
      <p>Contact No : <span><?= $fetch_reservations['contact_no']; ?></span></p>
      <p>Number of Pax : <span><?= $fetch_reservations['pax']; ?></span></p>
      <p>Reservation status : <span style="color:<?php if($fetch_reservations['reservation_status'] == 'pending'){ echo 'red'; }else{ echo 'green'; }; ?>"><?= $fetch_reservations['reservation_status']; ?></span> </p>

     
      
   </div>
   <?php
      }
      }else{
         echo '<p class="empty">no reservations made yet!</p>';
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