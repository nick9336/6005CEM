<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}

// for graph 1 
$monthsData = array();

for ($month = 1; $month <= 12; $month++) {
    $count = 0;
    $select_orders = $conn->prepare("SELECT COUNT(*) FROM `orders` WHERE MONTH(placed_on) = ?");
    $select_orders->execute([$month]);
    $fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC);
    $count = $fetch_orders['COUNT(*)'];
    array_push($monthsData, $count);
}

// for graph 2
$monthsRes = array();

for ($month = 1; $month <= 12; $month++) {
    $count = 0;
    $reservations = $conn->prepare("SELECT COUNT(*) FROM `reservations` WHERE MONTH(placed_on) = ?");
    $reservations->execute([$month]);
    $fetch_reservations = $reservations->fetch(PDO::FETCH_ASSOC);
    $count = $fetch_reservations['COUNT(*)'];
    array_push($monthsRes, $count);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>dashboard</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

   <?php include '../components/header.php' ?>

   <!-- admin dashboard section starts  -->

   <section class="dashboard">

      <h1 class="heading">Welcome Administrator</h1>

      <div class="box-container">
         <div class="box">
            <?php
            $total_sales = 0;

            $total_pendings = 0;
            $select_pendings = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
            $select_pendings->execute(['pending']);
            while ($fetch_pendings = $select_pendings->fetch(PDO::FETCH_ASSOC)) {
               $total_pendings += $fetch_pendings['total_price'];
            }

            $total_completes = 0;
            $select_completes = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
            $select_completes->execute(['completed']);
            while ($fetch_completes = $select_completes->fetch(PDO::FETCH_ASSOC)) {
               $total_completes += $fetch_completes['total_price'];
            }

            $total_sales = $total_completes + $total_pendings;

            ?>
            <h3><span>RM</span><?= $total_sales; ?></h3>
            <p>Total Sales</p>
         </div>



         <div class="box">
            <?php
            $select_users = $conn->prepare("SELECT * FROM `users`");
            $select_users->execute();
            $numbers_of_users = $select_users->rowCount();
            ?>
            <h3><?= $numbers_of_users; ?></h3>
            <p>Users</p>
         </div>

         <div class="box">
            <?php
            $select_orders = $conn->prepare("SELECT * FROM `orders`");
            $select_orders->execute();
            $numbers_of_orders = $select_orders->rowCount();
            ?>
            <h3><?= $numbers_of_orders; ?></h3>
            <p>Total Orders</p>
         </div>


         <div class="box">
            <?php
            $select_reservations = $conn->prepare("SELECT * FROM `reservations`");
            $select_reservations->execute();
            $numbers_of_reservations = $select_reservations->rowCount();
            ?>
            <h3><?= $numbers_of_reservations; ?></h3>
            <p>Total Reservations</p>
         </div>

         
         <div class="box">
            <?php
            $select_rest = $conn->prepare("SELECT * FROM `rest`");
            $select_rest->execute();
            $numbers_of_rests = $select_rest->rowCount();
            ?>
            <h3><?= $numbers_of_rests; ?></h3>
            <p>Employees</p>
         </div>

      </div>

      <div class="box-container" style="padding:30px 0px 0px 0px;">
      
         <div class="box">
            <h1 class="heading">Graph - Number of orders for each month</h1>

            <canvas id="myChart"></canvas>
         </div>

      </div>

      <div class="box-container" style="padding:30px 0px 0px 0px;">
      
         <div class="box">
            <h1 class="heading">Graph - Number of reservations for each month</h1>

            <canvas id="myChart2"></canvas>
         </div>

      </div>

   </section>

   <!-- admin dashboard section ends -->

   <!-- custom js file link  -->
   <script src="../js/admin_script.js"></script>

   <script>
      //graph 1
      var monthsData = <?= json_encode($monthsData); ?>;
      
      // Get the canvas element
      var ctx = document.getElementById('myChart').getContext('2d');

      // Define the chart data
      var data = {
         labels: ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'],
         datasets: [{
            label: 'Order Count',
            data: monthsData,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
         }]
      };

      // Create the chart
      var myChart = new Chart(ctx, {
         type: 'bar',
         data: data,
         options: {
            responsive: true,
            scales: {
               y: {
                  beginAtZero: true
               }
            }
         }
      });

      //graph 2
      var monthsRes = <?= json_encode($monthsRes); ?>;

      // Get the canvas element
      var ctx2 = document.getElementById('myChart2').getContext('2d');

      // Define the chart data
      var data2 = {
         labels: ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'],
         datasets: [{
            label: 'Reservation Count',
            data: monthsRes,
            backgroundColor: 'rgba(58, 144, 255)',
            borderColor: 'rgba(0, 0, 255)',
            borderWidth: 1
         }]
      };

      // Create the chart
      var myChart2 = new Chart(ctx2, {
         type: 'line',
         data: data2,
         options: {
            responsive: true,
            scales: {
               y: {
                  beginAtZero: true
               }
            }
         }
      });
   </script>
</body>
</html>
