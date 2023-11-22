<?php

include '../components/connect.php';

session_start();

$rest_id = $_SESSION['rest_id'];

if (!isset($rest_id)) {
    header('location:rest_login.php');
}

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['update'])) {
    // Check CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        // CSRF token is not valid, handle the error (log, redirect, etc.)
        $message[] = 'Invalid CSRF token. Please try again.';
    } else {
        $tid = $_POST['tid'];
        $tid = filter_var($tid, FILTER_SANITIZE_STRING);
        $table = $_POST['table'];
        $table = filter_var($table, FILTER_SANITIZE_STRING);
        $capacity = $_POST['capacity'];
        $capacity = filter_var($capacity, FILTER_SANITIZE_STRING);
        $status = $_POST['status'];
        $status = filter_var($status, FILTER_SANITIZE_STRING);

        $update_table = $conn->prepare("UPDATE `tables` SET `table` = ?, `status` = ?, `capacity` = ? WHERE `id` = ?");
        $update_table->execute([$table, $status, $capacity, $tid]);

        $message[] = 'Table updated!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Table</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/rest_style.css">

</head>

<body>

    <?php include '../components/rest_header.php' ?>

    <!-- update table section starts  -->

    <section class="update-product">

<h1 class="heading">Update Table</h1>

<?php
$update_id = $_GET['update'];
$show_tables = $conn->prepare("SELECT * FROM `tables` WHERE `id` = ?");
$show_tables->execute([$update_id]);
if ($show_tables->rowCount() > 0) {
    while ($fetch_tables = $show_tables->fetch(PDO::FETCH_ASSOC)) {
?>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="tid" value="<?= $fetch_tables['id']; ?>">
            <span>Update table</span>
            <input type="text" required placeholder="Enter table no" name="table" maxlength="100" class="box" value="<?= $fetch_tables['table']; ?>">
            <span>Update capacity</span>
            <input type="number" min="1" max="9999999999" required placeholder="Enter capacity" name="capacity" onkeypress="if(this.value.length == 10) return false;" class="box" value="<?= $fetch_tables['capacity']; ?>">
            <span>Update status</span>
            <select name="status" class="box" required>
                <option selected value="<?= $fetch_tables['status']; ?>"><?= $fetch_tables['status']; ?></option>
                <option value="Available">Available</option>
                <option value="Occupied">Occupied</option>
            </select>
            <div class="flex-btn">
                <input type="submit" value="Update" class="btn" name="update">
                <a href="manage_tables.php" class="option-btn">Back</a>
            </div>
        </form>
<?php
    }
} else {
    echo '<p class="empty">No tables added yet!</p>';
}
?>

</section>

    <!-- update table section ends -->

    <!-- custom js file link  -->
    <script src="../js/rest_script.js"></script>

</body>

</html>
