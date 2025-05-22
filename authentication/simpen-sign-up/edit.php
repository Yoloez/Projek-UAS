
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mahasiswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            height: 100px;
            resize: vertical;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Mahasiswa</h1>
    
        <?php
        // Include file koneksi database
        include_once("config.php");
        
        // Cek apakah ada ID yang dikirimkan
        if(!isset($_GET['id'])) {
            header("Location: index.php");
            exit();
        }
        
        $id = $_GET['id'];
        
        // Cek apakah form telah di-submit
        if(isset($_POST['update'])) {
            // $nim = $_POST['nim'];
            $nama = $_POST['name'];
            // $jurusan = $_POST['jurusan'];
            $email = $_POST['email'];
            $alamat = $_POST['password'];
            
            // Validasi form
            $errors = array();
            
            // if(empty($nim)) {
            //     $errors[] = "NIM tidak boleh kosong";
            // }
            
            if(empty($nama)) {
                $errors[] = "Nama tidak boleh kosong";
            }
            
            // if(empty($jurusan)) {
            //     $errors[] = "Jurusan tidak boleh kosong";
            // }
            
            if(empty($email)) {
                $errors[] = "Email tidak boleh kosong";
            } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format email tidak valid";
            }

            if(empty($password)){
                $errors[] = "Password tidak boleh kosong";
            }
            
            // Jika tidak ada error, update data
            if(empty($errors)) {
                $result = mysqli_query($conn, "UPDATE users SET 
                                            --    nim='$nim', 
                                               nama='$nama', 
                                            --    jurusan='$jurusan', 
                                               email='$email',
                                               password='$password', 
                                            --    alamat='$alamat' 
                                               WHERE id=$id");
                
                if($result) {
                    echo "<div style='padding: 10px; background-color: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 15px;'>";
                    echo "Data berhasil diperbarui. <a href='index.php'>Lihat Data</a>";
                    echo "</div>";
                } else {
                    echo "<div style='padding: 10px; background-color: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 15px;'>";
                    echo "Error: " . mysqli_error($conn);
                    echo "</div>";
                }
            } else {
                echo "<div style='padding: 10px; background-color: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 15px;'>";
                echo "<ul>";
                foreach($errors as $error) {
                    echo "<li>$error</li>";
                }
                echo "</ul>";
                echo "</div>";
            }
        }
        
        // Ambil data mahasiswa berdasarkan ID
        $result = mysqli_query($conn, "SELECT * FROM users WHERE id=$id");
        
        // Jika data tidak ditemukan, kembali ke halaman utama
        if(mysqli_num_rows($result) == 0) {
            header("Location: index.php");
            exit();
        }
        
        // Ambil data untuk ditampilkan di form
        $row = mysqli_fetch_assoc($result);
        // $nim = $row['nim'];
        $nama = $row['name'];
        // $jurusan = $row['jurusan'];
        $email = $row['email'];
        // $alamat = $row['alamat'];
        $password = $row['password'];
        ?>
        
        <form action="edit.php?id=<?php echo $id; ?>" method="post">
            
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="<?php echo $nama; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?php echo $email; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            
            <div style="margin-top: 20px;">
                <input type="submit" name="update" value="Update" class="btn">
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>