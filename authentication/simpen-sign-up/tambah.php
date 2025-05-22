<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mahasiswa</title>
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
        <h1>Tambah Mahasiswa</h1>
        
        <?php
        // Include file koneksi database
        include_once("config.php");
        
        // Cek apakah form telah di-submit
        if(isset($_POST['submit'])) {
            // $nim = $_POST['nim'];
            $nama = $_POST['nama'];
            // $jurusan = $_POST['jurusan'];
            $email = $_POST['email'];
            // $alamat = $_POST['alamat'];
            $password = $_POST['password'];

            
            // Validasi form
            $errors = array();
            
            // if(empty($nim)) {
            //     $errors[] = "NIM tidak boleh kosong";
            // }

            if(empty($nama)) {
                $errors[] = "Nama tidak boleh kosong";
            }
            
            if(empty($password)) {
                $errors[] = "Jurusan tidak boleh kosong";
            }
            
            if(empty($email)) {
                $errors[] = "Email tidak boleh kosong";
            } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format email tidak valid";
            }
            
            // Jika tidak ada error, simpan data
            if(empty($errors)) {
                $result = mysqli_query($conn, "INSERT INTO users(name, email, password) 
                                               VALUES('$nama', '$email', '$password')");
                
                if($result) {
                    echo "<div style='padding: 10px; background-color: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 15px;'>";
                    echo "Data berhasil ditambahkan. <a href='index.php'>Lihat Data</a>";
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
        ?>
        
        <form action="tambah.php" method="post">
            <!-- <div class="form-group">
                <label for="nim">NIM</label>
                <input type="text" name="nim" id="nim" required>
            </div> -->
            
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" required>
            </div>
            
            <!-- <div class="form-group">
                <label for="jurusan">Jurusan</label>
                <input type="text" name="jurusan" id="jurusan" required>
            </div> -->
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            
            <!-- <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea name="alamat" id="alamat"></textarea>
            </div> -->
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
                
            <div style="margin-top: 20px;">
                <input type="submit" name="submit" value="Simpan" class="btn">
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>