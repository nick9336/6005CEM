<?php
include '../components/connect.php';

session_start();

$rest_id = $_SESSION['rest_id'];

if (!isset($rest_id)) {
    header('location:rest_login.php');
}

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['add_product'])) {
   if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
       // CSRF token is not valid, handle the error (log, redirect, etc.)
       $message[] = 'Invalid CSRF token. Please try again.';
   } else {
       $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
       $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
       $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);

    // Validate the file upload
    if (!isset($_FILES['image']) || $_FILES['image']['error'] != 0) {
        $message[] = 'Please upload a valid image.';
    } else {
        $image = $_FILES['image']['name'];
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../uploaded_img/' . $image;

        if ($image_size > 2000000) {
         $message[] = 'Image size is too large';
     } else {
         // Ensure the uploaded_img directory exists
         if (!file_exists('../uploaded_img/')) {
             mkdir('../uploaded_img/');
         }
     
         // Move the uploaded file to the correct destination
         if (move_uploaded_file($image_tmp_name, $image_folder)) {
             // Check if the product already exists
             $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
             $select_products->execute([$name]);
     
             if ($select_products->rowCount() > 0) {
                 $message[] = 'Product already exists!';
             } else {
                 // Insert the new product into the database
                 $insert_product = $conn->prepare("INSERT INTO `products`(name, category, price, image) VALUES(?,?,?,?)");
                 $insert_product->execute([$name, $category, $price, $image]);
     
                 $message[] = 'New product added!';
             }
         } else {
             $message[] = 'Failed to move the uploaded file.';
         }
     }
    }
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
   }
}
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
    $delete_product_image->execute([$delete_id]);
    $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
    unlink('../uploaded_img/' . $fetch_delete_image['image']);
    $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
    $delete_product->execute([$delete_id]);
    $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
    $delete_cart->execute([$delete_id]);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    header('location:products.php');
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>products</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/rest_style.css">

</head>
<body>

<?php include '../components/rest_header.php' ?>

<!-- add products section starts  -->

<section class="add-products">
   <form action="" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
      <h3>Add product</h3>
      <input type="text" required placeholder="Enter product name" name="name" maxlength="100" class="box">
      <!-- Update input type to "number" for decimal values -->
      <input type="number" step="any" required placeholder="Enter product price" name="price" class="box" min="1" max="999999">
      <select name="category" class="box" required>
         <option value="" disabled selected>Select category --</option>
         <option value="Main Course">Main Course</option>
         <option value="Drinks">Drinks</option>
         <option value="Desserts">Desserts</option>
      </select>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp" required>
      <input type="submit" value="add product" name="add_product" class="btn">
   </form>
</section>

<!-- add products section ends -->

<!-- show products section starts  -->

<section class="show-products" style="padding-top: 0;">


   <div class="box-container">

   <?php
      $show_products = $conn->prepare("SELECT * FROM `products`");
      $show_products->execute();
      if($show_products->rowCount() > 0){
         while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
   ?>
   <div class="box">
      <img src="../uploaded_img/<?= $fetch_products['image']; ?>" alt="">
      <div class="flex">
         <div class="price"><span>RM</span><?= $fetch_products['price']; ?></div>
         <div class="category"><?= $fetch_products['category']; ?></div>
      </div>
      <div class="name"><?= $fetch_products['name']; ?></div>
      <div class="flex-btn">
         <a href="update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">update</a>
         <a href="products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('delete this product?');">delete</a>
      </div>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">No products added yet!</p>';
      }
   ?>

   </div>

</section>

<!-- show products section ends -->










<!-- custom js file link  -->
<script src="../js/rest_script.js"></script>

</body>
</html>