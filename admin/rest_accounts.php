<?php
include '../components/connect.php';
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_rest = $conn->prepare("DELETE FROM `rest` WHERE id = ?");
    $delete_rest->execute([$delete_id]);
    header('location:rest_accounts.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>restaurant accounts</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/rest_style.css">
</head>
<body>

<?php include '../components/header.php' ?>

<!-- restaurant accounts section starts  -->

<section class="accounts">

    <h1 class="heading">restaurant accounts</h1>

    <div class="box-container">

        <!-- Register New Account Button -->
        <div class="box">
            <p>Register New Account</p>
            <a href="register_rest.php" class="option-btn">register</a>
        </div>

        <!-- Displaying Existing Restaurant Accounts -->
        <?php
        $select_account = $conn->prepare("SELECT * FROM `rest`");
        $select_account->execute();
        if ($select_account->rowCount() > 0) {
            while ($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <div class="box">
                    <p> Rest id : <span><?= htmlspecialchars($fetch_accounts['id']); ?></span> </p>
                    <p> Username : <span><?= htmlspecialchars($fetch_accounts['name']); ?></span> </p>
                    <div class="flex-btn">
                        <!-- Delete Button with Confirmation -->
                        <a href="rest_accounts.php?delete=<?= htmlspecialchars($fetch_accounts['id']); ?>"
                           class="delete-btn" onclick="return confirm('delete this account?');">delete</a>
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

<!-- restaurant accounts section ends -->

<!-- custom js file link  -->
<script src="../js/rest_script.js"></script>

</body>
</html>
