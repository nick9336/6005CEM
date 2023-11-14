<?php

include '../components/connect.php';

session_start();

$rest_id = $_SESSION['rest_id'];

if(!isset($rest_id)){
   header('location:rest_login.php');
}

if(isset($_POST['submit'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);

   if(!empty($name)){
      $select_name = $conn->prepare("SELECT * FROM `rest` WHERE name = ?");
      $select_name->execute([$name]);
      if($select_name->rowCount() > 0){
         $message[] = 'Username already exists!';
      }else{
         $update_name = $conn->prepare("UPDATE `rest` SET name = ? WHERE id = ?");
         $update_name->execute([$name, $rest_id]);
      }
   }

   $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
   $select_old_pass = $conn->prepare("SELECT password FROM `rest` WHERE id = ?");
   $select_old_pass->execute([$rest_id]);
   $fetch_prev_pass = $select_old_pass->fetch(PDO::FETCH_ASSOC);
   $prev_pass = $fetch_prev_pass['password'];
   $old_pass = sha1($_POST['old_pass']);
   $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);
   $new_pass = sha1($_POST['new_pass']);
   $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
   $confirm_pass = sha1($_POST['confirm_pass']);
   $confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_STRING);

   if($old_pass != $empty_pass){
      if($old_pass != $prev_pass){
         $message[] = 'Old password not matched!';
      }elseif($new_pass != $confirm_pass){
         $message[] = 'Confirm password not matched!';
      }else{
         if($new_pass != $empty_pass){
            $update_pass = $conn->prepare("UPDATE `rest` SET password = ? WHERE id = ?");
            $update_pass->execute([$confirm_pass, $rest_id]);
            $message[] = 'Password updated successfully!';
         }else{
            $message[] = 'Please enter a new password!';
         }
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>profile update</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/rest_style.css">

</head>
<body>

<?php include '../components/rest_header.php' ?>

<!-- rest profile update section starts  -->

<section class="form-container">

   <form action="" method="POST">
      <h3>update profile</h3>
      <input type="text" name="name" maxlength="20" class="box" placeholder="Enter new username" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="old_pass" maxlength="20" placeholder="Enter old password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="new_pass" maxlength="20" placeholder="Enter new password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="confirm_pass" maxlength="20" placeholder="Confirm new password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="update now" name="submit" class="btn">
   </form>

</section>

<!-- rest profile update section ends -->









<!-- custom js file link  -->
<script src="../js/rest_script.js"></script>

</body>
</html>