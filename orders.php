<!--security added
-->
<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';

}

if (!isset($_SESSION['initiated'])) {
   session_regenerate_id();
   $_SESSION['initiated'] = true;
};

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>orders</title>

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
   <h3>orders</h3>
   <p><a href="html.php">home</a> <span> / orders</span></p>
</div>

<section class="orders">

   <h1 class="title">your orders</h1>

   <div class="box-container">

   <?php
      if($user_id == ''){
         echo '<p class="empty">please login to see your orders</p>';
      }else{

         //sql injection prevent using prepare statement
         $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = :user_id");
         $select_orders->execute(['user_id' => $user_id]);
         
         if($select_orders->rowCount() > 0){
            while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
   ?>

   
   <div class="box">
      <!--htmlspecialchars use to prevent xss attacks --- Cross-Site Scripting (XSS) Prevention   -->
      <p>placed on : <span><?=htmlspecialchars($fetch_orders['placed_on']); ?></span></p>
      <p>name : <span><?= htmlspecialchars($fetch_orders['name']); ?></span></p>
      <p>email : <span><?= htmlspecialchars($fetch_orders['email']); ?></span></p>
      <p>number : <span><?= htmlspecialchars($fetch_orders['number']); ?></span></p>
      <p>payment method : <span><?= htmlspecialchars($fetch_orders['method']); ?></span></p>
      <p>your orders : <span><?= htmlspecialchars($fetch_orders['total_products']); ?></span></p>
      <p>total price : <span>RM<?= htmlspecialchars($fetch_orders['total_price']); ?>/-</span></p>
      <p> payment status : <span style="color:<?php if(htmlspecialchars($fetch_orders['payment_status']) == 'pending'){ echo 'red'; } else { echo 'green'; }; ?>"><?= htmlspecialchars($fetch_orders['payment_status']); ?></span> </p>
   </div>
   <?php
      }
      }else{
         echo '<p class="empty">no orders placed yet!</p>';
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