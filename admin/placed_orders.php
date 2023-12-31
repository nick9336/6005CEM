<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (!isset($_SESSION['csrf_token'])) {
   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['update_payment'])) {

   $order_id = $_POST['order_id'];
   $payment_status = $_POST['payment_status'];

   $update_status = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
   $update_status->execute([$payment_status, $order_id]);
   $message[] = 'Payment status updated!';
}


if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];

   // CSRF protection
   if (isset($_GET['token']) && hash_equals($_SESSION['csrf_token'], $_GET['token'])) {
       $delete_orders = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
       $delete_orders->execute([$delete_id]);
       header('location:placed_orders.php');
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
    <title>placed orders</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

    <?php include '../components/header.php' ?>

    <!-- placed orders section starts  -->

    <section class="placed-orders">

        <h1 class="heading">placed orders</h1>

        <div class="box-container">

            <?php
            $select_orders = $conn->prepare("SELECT * FROM `orders`");
            $select_orders->execute();
            if ($select_orders->rowCount() > 0) {
                while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
            ?>
                    <div class="box">
                        <p>Date Placed on : <span><?= htmlspecialchars($fetch_orders['placed_on'], ENT_QUOTES, 'UTF-8'); ?></span> </p>
                        <p>Name : <span><?= htmlspecialchars($fetch_orders['name'], ENT_QUOTES, 'UTF-8'); ?></span> </p>
                        <p>Email : <span><?= htmlspecialchars($fetch_orders['email'], ENT_QUOTES, 'UTF-8'); ?></span> </p>
                        <p>Number : <span><?= htmlspecialchars($fetch_orders['number'], ENT_QUOTES, 'UTF-8'); ?></span> </p>
                        <p>Total price : <span>RM<?= htmlspecialchars($fetch_orders['total_price'], ENT_QUOTES, 'UTF-8'); ?>/-</span> </p>
                        <p>Payment method : <span><?= htmlspecialchars($fetch_orders['method'], ENT_QUOTES, 'UTF-8'); ?></span> </p>
                        <form action="" method="POST">
                            <input type="hidden" name="order_id" value="<?= htmlspecialchars($fetch_orders['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <select name="payment_status" class="drop-down">
                                <option value="" selected disabled><?= htmlspecialchars($fetch_orders['payment_status'], ENT_QUOTES, 'UTF-8'); ?></option>
                                <option value="Pending">Pending</option>
                                <option value="Completed">Completed</option>
                            </select>
                            <div class="flex-btn">
                                <input type="submit" value="update" class="btn" name="update_payment">
                                <a href="placed_orders.php?delete=<?= $fetch_orders['id']; ?>&token=<?= $csrf_token ?>"
                                 class="delete-btn" onclick="return confirm('Delete this order?');">delete</a>
                            </div>
                        </form>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">No orders placed yet!</p>';
            }
            ?>

        </div>

    </section>

    <!-- placed orders section ends -->

    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

</body>

</html>
