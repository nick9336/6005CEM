<?php

include 'connect.php';

session_start();
session_unset();
session_destroy();

header('location:../rest/rest_login.php');

?>