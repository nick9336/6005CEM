<!--security added
1.csrf_token
2.session 

3.validation

-->
<?php

if(isset($_POST['add_to_cart'])){
    // CSRF token verification
    if (isset($_POST['csrf_token'])) {
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die('CSRF token mismatch');
        }
    } else {
        die('No CSRF token provided');
    }

   if($user_id == ''){
      header('location:login.php');
   }else{

      $pid = filter_var($_POST['pid'], FILTER_SANITIZE_STRING);
      $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
      $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
      $qty = filter_var($_POST['qty'], FILTER_SANITIZE_NUMBER_INT);
      $image = filter_var($_POST['image'], FILTER_SANITIZE_STRING); 


      //error handling
      if (!filter_var($qty, FILTER_VALIDATE_INT) || $qty < 1) {
         $message[] = 'Invalid quantity';
     }
     
     if (!filter_var($price, FILTER_VALIDATE_FLOAT) || $price < 0) {
         $message[] = 'Invalid price';
     }
     
     if (!filter_var($pid, FILTER_VALIDATE_INT)) {
         $message[] = 'Invalid product ID';
     }
     try {
      $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
      $check_cart_numbers->execute([$name, $_SESSION['user_id']]);
  
      if ($check_cart_numbers->rowCount() > 0) {
          $message[] = 'Already added to cart!';
      } else {
        $insert_cart = $conn->prepare("INSERT INTO `cart` (user_id, pid, name, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)"); // Update the prepared statement to include the image
        $insert_cart->execute([$_SESSION['user_id'], $pid, $name, $price, $qty, $image]); // Pass the sanitized image filename to the execute method
        $message[] = 'Added to cart!';
      }
  } catch (PDOException $e) {
      // Handle the error
      $message[] = 'Error: ' . $e->getMessage();
  }
}

}

?>