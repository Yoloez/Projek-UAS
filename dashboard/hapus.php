<?php
include '../koneksi.php';
$id = $_GET['food_id'];
mysqli_query($conn, "DELETE FROM foods WHERE food_id=$id");
header("Location: dashboard.php");
?>