<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet" href="style.css" />
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
  if (isset($_POST['sign-up'])) {
      $name = $_POST['name'];
      $email = $_POST['email'];
      $password = $_POST['password'];

      $checkEmail = "SELECT * FROM users WHERE email = '$email'";   
      $emailcheck = mysqli_query($conn, $checkEmail);
      $checkPassword = "SELECT * FROM users WHERE password = '$password'";
      $passwordcheck = mysqli_query($conn, $checkPassword);

      if (mysqli_num_rows($emailcheck) > 0) {
          echo "<script>alert('Email already exists!');</script>";
      } 
      else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $insertQuery = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashedPassword')";
        if (mysqli_query($conn, $insertQuery)) {
            echo "<script>alert('Registration successful!');</script>";
            header("Location: ../../user/index.php");
            exit();
        } else {
            echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
        }
    }
  }
  ?>
<nav style="position: relative;">
  <a class="navbar-brand" href="../landing/index.php" style="color: #fff; text-decoration:none; position:absolute; left:50%; width:100%; background-color:aqua" >Orbyt</a>

</nav>
    <div class="card">
      <h1 style="color: #fff; font-family: Cormorant; font-size: 54px; font-style: normal; font-weight: 700; line-height: normal">Sign-Up</h1>
      <form method="POST">
        <div class="input">
          <input type="text" name="name" placeholder="username" required />
          <input type="email" name="email" placeholder="email" required/>
          <input type="password" name="password" placeholder="password" required />
          <button type="submit" name="sign-up">Sign-up</button>
        </div>
        
        <p>already have an account? <span><a href="../login/login.php" class="login-link">login</a></span></p>
    </div>

  </body>
</html>
