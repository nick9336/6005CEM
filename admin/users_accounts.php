<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit; // Ensure script stops execution after redirect
}

if (!isset($_SESSION['csrf_token'])) {
   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];

   // CSRF protection
   if (isset($_GET['token']) && hash_equals($_SESSION['csrf_token'], $_GET['token'])) {
      $delete_users = $conn->prepare("DELETE FROM `users` WHERE id = ?");
      $delete_users->execute([$delete_id]);
      $delete_order = $conn->prepare("DELETE FROM `orders` WHERE user_id = ?");
      $delete_order->execute([$delete_id]);
      $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $delete_cart->execute([$delete_id]);
      header('location:users_accounts.php');       
   } else {

       echo 'Invalid CSRF Token';
       exit;
   }
}

// Generate CSRF token
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>users accounts</title>

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

    <?php include '../components/header.php' ?>

    <!-- user accounts section starts -->

    <section class="accounts">

        <h1 class="heading">users accounts</h1>

        <div class="box-container">

            <?php
            $select_account = $conn->prepare("SELECT * FROM `users`");
            $select_account->execute();
            if ($select_account->rowCount() > 0) {
                while ($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)) {
            ?>
                    <div class="box">
                        <p> Name : <span><?= htmlspecialchars($fetch_accounts['name']); ?></span> </p>
                        <p> Email : <span><?= htmlspecialchars($fetch_accounts['email']); ?></span> </p>
                        <p> Contact : <span><?= htmlspecialchars($fetch_accounts['number']); ?></span> </p>

                        <div class="flex-btn">
                        <a href="users_accounts.php?delete=<?= $fetch_accounts['id']; ?>&token=<?= $csrf_token ?>"
                                 class="delete-btn" onclick="return confirm('Delete this account?');">delete</a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">no accounts available</p>';
            }
            ?>

        </div>

    </section>

    <!-- user accounts section ends -->

    <!-- custom js file link -->
    <script src="../js/admin_script.js"></script>

</body>

</html>
