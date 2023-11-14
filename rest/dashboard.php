<?php

include '../components/connect.php';

session_start();

$rest_id = $_SESSION['rest_id'];

if(!isset($rest_id)){
   header('location:rest_login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>dashboard</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/rest_style.css">

</head>
<body>

<?php include '../components/rest_header.php' ?>

<!-- rest dashboard section starts  -->

<section class="dashboard">

   <h1 class="heading">Welcome Employee: <?= $fetch_profile['name']; ?></h1>

   <div class="box-container">
 

   <div class="box">
      <?php
         $total_completes = 0;
         $select_completes = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
         $select_completes->execute(['completed']);
         while($fetch_completes = $select_completes->fetch(PDO::FETCH_ASSOC)){
            $total_completes += $fetch_completes['total_price'];
         }
      ?>
      <h3><span>RM</span><?= $total_completes; ?></h3>
      <p>Completed Payments</p>
   </div>

   <div class="box">
      <?php
         $total_pendings = 0;
         $select_pendings = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
         $select_pendings->execute(['pending']);
         while($fetch_pendings = $select_pendings->fetch(PDO::FETCH_ASSOC)){
            $total_pendings += $fetch_pendings['total_price'];
         }
      ?>
      <h3><span>RM</span><?= $total_pendings; ?></h3>
      <p>Pending Payments</p>
   </div>

   <div class="box">
      <?php
         $select_products = $conn->prepare("SELECT * FROM `products`");
         $select_products->execute();
         $numbers_of_products = $select_products->rowCount();
      ?>
      <h3><?= $numbers_of_products; ?></h3>
      <p>Menu Items</p>
   </div>

   <div class="box">
      <?php
         $select_orders = $conn->prepare("SELECT * FROM `orders` where payment_status = ?");
         $select_orders->execute(['pending']);
         $numbers_of_orders = $select_orders->rowCount();
      ?>
      <h3><?= $numbers_of_orders; ?></h3>
      <p>Pending Orders</p>
   </div>

   
   <div class="box">
      <?php
         $select_reservations = $conn->prepare("SELECT * FROM `reservations` WHERE reservation_status = ?");
         $select_reservations->execute(['pending']);
         $numbers_of_reservations = $select_reservations->rowCount();
      ?>
      <h3><?= $numbers_of_reservations; ?></h3>
      <p>Pending Reservations</p>
   </div>

   <div class="box">
      <?php
         $select_tables = $conn->prepare("SELECT * FROM `tables` WHERE status = ?");
         $select_tables->execute(['available']);
         $numbers_of_tables = $select_tables->rowCount();
      ?>
      <h3><?= $numbers_of_tables; ?></h3>
      <p>Available Tables</p>
   </div>
   

   <div class="box">
      <?php
         $select_messages = $conn->prepare("SELECT * FROM `messages`");
         $select_messages->execute();
         $numbers_of_messages = $select_messages->rowCount();
      ?>
      <h3><?= $numbers_of_messages; ?></h3>
      <p>Messages</p>
   </div>

   </div>

</section>

<!-- rest dashboard section ends -->









<!-- custom js file link  -->
<script src="../js/rest_script.js"></script>

</body>
</html>