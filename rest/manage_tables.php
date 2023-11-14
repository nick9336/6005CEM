<?php

include '../components/connect.php';

session_start();

$rest_id = $_SESSION['rest_id'];

if (!isset($rest_id)) {
    header('location:rest_login.php');
}

if (isset($_POST['add_table'])) {
    $table = $_POST['table'];
    $table = filter_var($table, FILTER_SANITIZE_STRING);
    $capacity = $_POST['capacity'];
    $capacity = filter_var($capacity, FILTER_SANITIZE_STRING);
    $status = $_POST['status'];
    $status = filter_var($status, FILTER_SANITIZE_STRING);

    $select_tables = $conn->prepare("SELECT * FROM `tables` WHERE `table` = ?");
    $select_tables->execute([$table]);

    if ($select_tables->rowCount() > 0) {
        $message[] = 'Table already exists!';
    } else {
        $insert_table = $conn->prepare("INSERT INTO `tables`(`table`, `status`, `capacity`) VALUES(?,?,?)");
        $insert_table->execute([$table, $status, $capacity]);

        $message[] = 'New table added!';
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_table = $conn->prepare("DELETE FROM `tables` WHERE `id` = ?");
    $delete_table->execute([$delete_id]);
    header('location:manage_tables.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tables</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/rest_style.css">

</head>

<body>

    <?php include '../components/rest_header.php' ?>

    <section class="add-products">

        <form action="" method="POST" enctype="multipart/form-data">
            <h3>Add table</h3>
            <input type="text" required placeholder="Enter table" name="table" maxlength="100" class="box">
            <input type="number" min="0" max="9999999999" required placeholder="Enter capacity" name="capacity"
                onkeypress="if(this.value.length == 10) return false;" class="box">
            <select name="status" class="box" required>
                <option value="" disabled selected>Select status --</option>
                <option value="Available">Available</option>
                <option value="Occupied">Occupied</option>
            </select>
            <input type="submit" value="Add Table" name="add_table" class="btn">
        </form>

    </section>

    <section class="show-products" style="padding-top: 0;">

        <div class="box-container">

            <?php
            $show_tables = $conn->prepare("SELECT * FROM `tables`");
            $show_tables->execute();
            if ($show_tables->rowCount() > 0) {
                while ($fetch_tables = $show_tables->fetch(PDO::FETCH_ASSOC)) {
            ?>
                    <div class="box">
                        <div class="flex">
                            <div class="price"><span>Capacity: </span><?= $fetch_tables['capacity']; ?></div>
                            <div class="category"><span>Status: </span><?= $fetch_tables['status']; ?></div>
                        </div>
                        <div class="name"><span>Table No: </span><?= $fetch_tables['table']; ?></div>
                        <div class="flex-btn">
                            <a href="update_table.php?update=<?= $fetch_tables['id']; ?>" class="option-btn">update</a>
                            <a href="manage_tables.php?delete=<?= $fetch_tables['id']; ?>" class="delete-btn"
                                onclick="return confirm('Delete this table?');">delete</a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">No tables added yet!</p>';
            }
            ?>

        </div>

    </section>

    <!-- custom js file link  -->
    <script src="../js/rest_script.js"></script>

</body>

</html>
