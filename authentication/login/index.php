<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.9">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Cormorant:ital,wght@0,300..700;1,300..700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Italiana&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />
</head>
<body>
<?php
include '../../koneksi.php'; 

if (isset($_POST['login'])) {
    $name = $_POST['name'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE name = '$name'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            session_start();
            if ($row['role'] === 'admin') {
                $_SESSION['admin'] = $row['name'];
                echo "<script>alert('Login sebagai Admin berhasil!');</script>";
                header("Location: ../../dashboard/index.php"); 
                exit();
            } else {
                $_SESSION['user'] = $row['name'];
                echo "<script>alert('Login sebagai User berhasil!');</script>";
                header("Location: ../../user/index.php"); 
                exit();
            }
        } else {
            echo "<script>alert('Password salah!');</script>";
        }
    } else {
        echo "<script>alert('Username tidak ditemukan!');</script>";
    }
}
?>
    <div class="card">
      <h1 style="color: #fff; font-family: Cormorant; font-size: 54px; font-style: normal; font-weight: 700; line-height: normal">Login</h1>
      <form action="" method="POST">
        <div class="input">
            <input type="text" name="name" placeholder="username" />
            <input type="password" name="password" placeholder="password" />
            <button type="submit" name="login">Login</button>
        </div>
        
        <p>don't have an account? <span><a href="../sign-up/index.php" style="color: white; text-decoration: none;">sign-up here</a></span></p>
    </div>
</body>
</html>