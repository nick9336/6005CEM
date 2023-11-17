<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

if (!isset($_SESSION['csrf_token'])) {
   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];

   // CSRF protection
   if (isset($_GET['token']) && hash_equals($_SESSION['csrf_token'], $_GET['token'])) {
       $delete_message = $conn->prepare("DELETE FROM `messages` WHERE id = ?");
       $delete_message->execute([$delete_id]);
       header('location:messages.php');
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
   <title>messages</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/header.php' ?>

<!-- messages section starts  -->

<section class="messages">

   <h1 class="heading">messages</h1>

   <div class="box-container">

   <?php
        $select_messages = $conn->prepare("SELECT * FROM `messages`");
        $select_messages->execute();
        if ($select_messages->rowCount() > 0) {
            while ($fetch_messages = $select_messages->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <div class="box">
                    <p> name : <span><?= $fetch_messages['name']; ?></span> </p>
                    <p> number : <span><?= $fetch_messages['number']; ?></span> </p>
                    <p> email : <a href="mailto:<?= $fetch_messages['email']; ?>"><?= $fetch_messages['email']; ?></a>
                    </p>
                    <p> message : <span><?= $fetch_messages['message']; ?></span> </p>
                    <a href="messages.php?delete=<?= $fetch_messages['id']; ?>&token=<?= $csrf_token ?>"
                       class="delete-btn" onclick="return confirm('Delete this message?');">delete</a>
                </div>
                <?php
            }
        } else {
            echo '<p class="empty">you have no messages</p>';
        }
        ?>

   </div>

</section>

<!-- messages section ends -->









<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>