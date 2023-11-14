<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
};

if(isset($_POST['update_reservation'])){

   $reservation_id = $_POST['reservation_id'];
   $reservation_status = $_POST['reservation_status'];
   $update_status = $conn->prepare("UPDATE `reservations` SET reservation_status = ? WHERE id = ?");
   $update_status->execute([$reservation_status, $reservation_id]);
   $message[] = 'reservation status updated!';

}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_reservation = $conn->prepare("DELETE FROM `reservations` WHERE id = ?");
   $delete_reservation->execute([$delete_id]);
   header('location:placed_reservations.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>placed reservations</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/header.php' ?>

<!-- placed reservations section starts  -->

<section class="placed-orders">

   <h1 class="heading">placed reservations</h1>

   <div class="box-container">

   <?php
      $select_reservations = $conn->prepare("SELECT * FROM `reservations`");
      $select_reservations->execute();
      if($select_reservations->rowCount() > 0){
         while($fetch_reservations = $select_reservations->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p> Reservation Date : <span><?= $fetch_reservations['placed_on']; ?></span> </p>
      <p> Time of reservation : <span><?= $fetch_reservations['time_placed']; ?></span> </p>
      <p> Name : <span><?= $fetch_reservations['name']; ?></span> </p>
      <p> Email : <span><?= $fetch_reservations['email']; ?></span> </p>
      <p> Number : <span><?= $fetch_reservations['contact_no']; ?></span> </p>
      <p> Pax : <span><?= $fetch_reservations['pax']; ?></span> </p>
   </div>
   <?php
      }
   }else{
      echo '<p class="empty">No reservations placed yet!</p>';
   }
   ?>

   </div>

</section>

<!-- placed reservations section ends -->









<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>