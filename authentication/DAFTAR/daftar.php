<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "db_pertanian";

$conn = mysqli_connect($servername, $username, $password, $database);
if(!$conn){
    die("Koneksi Gagal : " . mysqli_connect_error());
} else {
    echo "Koneksi Berhasil";
}

?>