<?php

include '../components/connect.php';

session_start();

$rest_id = $_SESSION['rest_id'];

if (!isset($rest_id)) {
    header('location:rest_login.php');
}

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['update_payment'])) {
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token mismatch");
    }

    $order_id = filter_var($_POST['order_id'], FILTER_VALIDATE_INT);
    $payment_status = filter_var($_POST['payment_status'], FILTER_SANITIZE_STRING);

    if ($order_id === false || empty($payment_status)) {
        // Invalid input, handle the error (log, redirect, etc.)
        $message[] = 'Invalid input. Please check your data.';
    } else {
        $update_status = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
        $update_status->execute([$payment_status, $order_id]);
        $message[] = 'Payment status updated!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>placed orders</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/rest_style.css">

</head>
<body>

<?php include '../components/rest_header.php' ?>

<!-- placed orders section starts  -->

<section class="placed-orders">

   <h1 class="heading">placed orders</h1>

   <div class="box-container">

   <?php
      $select_orders = $conn->prepare("SELECT * FROM `orders`");
      $select_orders->execute();
      if($select_orders->rowCount() > 0){
         while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p> Date Placed on : <span><?= $fetch_orders['placed_on']; ?></span> </p>
      <p> Name : <span><?= $fetch_orders['name']; ?></span> </p>
      <p> Email : <span><?= $fetch_orders['email']; ?></span> </p>
      <p> Number : <span><?= $fetch_orders['number']; ?></span> </p>
      <p> Total price : <span>RM<?= $fetch_orders['total_price']; ?>/-</span> </p>
      <p> Payment method : <span><?= $fetch_orders['method']; ?></span> </p>
      <form action="" method="POST">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
      <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
      <select name="payment_status" class="drop-down">
         <option value="" selected disabled><?= $fetch_orders['payment_status']; ?></option>
         <option value="Pending">Pending</option>
         <option value="Completed">Completed</option>
      </select>
      <div class="flex-btn">
         <input type="submit" value="update" class="btn" name="update_payment">
      </div>
      </form>
   </div>
   <?php
      }
   }else{
      echo '<p class="empty">No orders placed yet!</p>';
   }
   ?>

   </div>

</section>

<!-- placed orders section ends -->









<!-- custom js file link  -->
<script src="../js/rest_script.js"></script>

</body>
</html>