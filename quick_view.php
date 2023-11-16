
<!--security added-->
<?php

include 'components/connect.php';

session_start();
if (empty($_SESSION['csrf_token'])) {
   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
//sesssion regenearate id to prevent fixation attacks
if (!isset($_SESSION['initiated'])) {
   session_regenerate_id();
   $_SESSION['initiated'] = true;
}

//session to check the user id 
if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}


include 'components/add_cart.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>quick view</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="quick-view">

   <h1 class="title">quick view</h1>

   <?php
      $pid = $_GET['pid'];
      $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
      $select_products->execute([$pid]);
      if($select_products->rowCount() > 0){
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
      <input type="hidden" name="pid" value="<?= htmlspecialchars($fetch_products['id']); ?>">
      <input type="hidden" name="name" value="<?= htmlspecialchars($fetch_products['name']); ?>">
      <input type="hidden" name="price" value="<?= htmlspecialchars($fetch_products['price']); ?>">
      <input type="hidden" name="image" value="<?= htmlspecialchars($fetch_products['image']); ?>">
      <img src="uploaded_img/<?= htmlspecialchars($fetch_products['image']); ?>" alt="">
      <a href="category.php?category=<?= htmlspecialchars($fetch_products['category']); ?>" class="cat"><?= htmlspecialchars($fetch_products['category']); ?></a>
      <div class="name"><?= htmlspecialchars($fetch_products['name']); ?></div>
      <div class="flex">
         <div class="price"><span>RM</span><?= htmlspecialchars($fetch_products['price']); ?></div>
         <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
      </div>
      <button type="submit" name="add_to_cart" class="cart-btn">add to cart</button>
   </form>
   <?php
         }
      }else{
         echo '<p class="empty">no products added yet!</p>';
      }
   ?>

</section>
















<?php include 'components/footer.php'; ?>


<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>


</body>
</html>


<!--security added-->