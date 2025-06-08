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
session_start();
include '../../koneksi.php';

// Redirect jika sudah login    
if (isset($_SESSION['username'])) {
    $redirect = ($_SESSION['role'] === 'admin') ? '../../dashboard/dashboard.php' : '../../user/user.php';
    header("Location: $redirect");
    exit();
}

$login_error = '';

if (isset($_POST['login'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE name = '$name'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['name'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['user_id'] = $row['id'];

            if ($row['role'] === 'admin') {
                header("Location: ../../dashboard/dashboard.php");
                exit();
            } else {
                header("Location: ../../user/user.php");    
                exit();
            }
        } else {
            $login_error = "Password salah!";
        }
    } else {
        $login_error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=0.9" />
    <title>Login</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cormorant:wght@700&family=Inter:wght@400&family=Italiana&family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
</head>
<body>

<a href="../../index.php" style="position: absolute; left:2rem; top: 2rem; text-decoration:none;color:white; font-size: 35px">Orbyt</a>
    <div class="card">
      <h1 style="color: #fff; font-family: Cormorant; font-size: 54px; font-style: normal; font-weight: 700; line-height: normal">Login</h1>
      <form action="" method="POST">
        <div class="input">
            <input type="text" name="name" placeholder="username" required/>
            <input type="password" name="password" placeholder="password" required/>
            <button type="submit" name="login">Login</button>
        </div>
        
        <p>don't have an account? <span><a href="../sign-up/sign-up.php" style="color: white; text-decoration: none;">sign-up here</a></span></p>
    </div>
</body>
</html>