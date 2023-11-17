<?php

include '../components/connect.php';

session_start();

if (!isset($_SESSION['csrf_token'])) {
   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['submit'])) {

   if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
      // Invalid CSRF token, handle accordingly
      exit('Invalid CSRF token');
  } 

    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $pass = $_POST['pass'];

    $select_admin = $conn->prepare("SELECT id, password FROM `admin` WHERE name = ?");
    $select_admin->execute([$name]);

    if ($select_admin->rowCount() > 0) {
        $fetch_admin = $select_admin->fetch(PDO::FETCH_ASSOC);
        $hashed_password = $fetch_admin['password'];

        // Verify the password
        if (password_verify($pass, $hashed_password)) {
            // Regenerate session ID
            session_regenerate_id(true);

            // Set session variable
            $_SESSION['admin_id'] = $fetch_admin['id'];
            header('location:dashboard.php');
        } else {
            // Log failed login attempts
            // For enhanced security, consider delaying the response to thwart brute force attacks
            $message[] = 'Invalid username or password.';
        }
    } else {
        // Log failed login attempts
        // For enhanced security, consider delaying the response to thwart brute force attacks
        $message[] = 'Invalid username or password.';
    }

    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>

    <?php
    if (isset($message)) {
        foreach ($message as $message) {
            echo '
      <div class="message">
         <span>' . $message . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
        }
    }
    ?>

    <!-- admin login form section starts  -->

    <section class="form-container">
        <form action="" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <h3>Admin Panel Login</h3>
            <input type="text" name="name" maxlength="20" required placeholder="Admin Username" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="pass" maxlength="20" required placeholder="Admin Password" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="login now" name="submit" class="btn">
        </form>
    </section>

    <!-- admin login form section ends -->

</body>

</html>
