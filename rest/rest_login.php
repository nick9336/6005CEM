<?php
include '../components/connect.php';

session_start();

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['submit'])) {
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        // Token mismatch, handle accordingly (e.g., log the attempt, show an error)
        die("CSRF token mismatch");
    }

    $name = $_POST['name'];
    $pass = $_POST['pass'];

    $select_rest = $conn->prepare("SELECT id, password FROM `rest` WHERE name = ?");
    $select_rest->execute([$name]);

    if ($select_rest->rowCount() > 0) {
        $fetch_rest = $select_rest->fetch(PDO::FETCH_ASSOC);
        $hashed_password = $fetch_rest['password'];

        if (password_verify($pass, $hashed_password)) {
            $_SESSION['rest_id'] = $fetch_rest['id'];
            session_regenerate_id(true); // Regenerate session ID
            header('location: dashboard.php');
        } else {
            $message[] = 'Incorrect username or password!';
        }
    } else {
        $message[] = 'Incorrect username or password!';
    }
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
   <link rel="stylesheet" href="../css/rest_style.css">

</head>
<body>

<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<!-- rest login form section starts  -->
<section class="form-container">
   <form action="" method="POST">
      <!-- Add CSRF Token input field -->
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
      
      <h3>Restaurant Panel Login</h3>
      <input type="text" name="name" maxlength="20" required placeholder="Employee Username" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" maxlength="20" required placeholder="Employee Password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="login now" name="submit" class="btn">
   </form>
</section>
<!-- rest login form section ends -->

</body>
</html>
