<?php
include '../koneksi.php';

// Ambil semua kategori untuk pilihan dropdown
$kategori = mysqli_query($conn, "SELECT * FROM category");

if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $kategori_id = $_POST['kategori_id'];

    // Pindahkan file yang diunggah
    $target_dir = '../uploads/';
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Buat direktori jika belum ada
    }
    $target_file = $target_dir . basename($gambar);

    if (move_uploaded_file($tmp, $target_file)) {
        // Masukkan data makanan baru ke database
        // Sebaiknya gunakan prepared statements untuk menghindari SQL Injection
        $nama = mysqli_real_escape_string($conn, $nama);
        $deskripsi = mysqli_real_escape_string($conn, $deskripsi);
        $harga = mysqli_real_escape_string($conn, $harga);
        $stok = mysqli_real_escape_string($conn, $stok);
        $kategori_id = mysqli_real_escape_string($conn, $kategori_id);
        $gambar_db = mysqli_real_escape_string($conn, $gambar);

        $query = "INSERT INTO foods (name, description, price, stock, kategori_id, image_url)
                  VALUES ('$nama', '$deskripsi', '$harga', '$stok', '$kategori_id', '$gambar_db')";

        if (mysqli_query($conn, $query)) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Gagal menambahkan: " . mysqli_error($conn);
        }
    } else {
        echo "Gagal mengunggah gambar.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Food/Drink</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px; /* Padding untuk layar kecil */
        }

        .form-tambah {
            background-color: #ffffff;
            padding: 35px 45px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
            width: 100%;
            max-width: 650px; /* Lebar maksimum form */
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-tambah h2 {
            color: #333;
            margin-bottom: 30px;
            font-weight: 600;
            text-align: center;
            font-size: 1.8rem;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            font-size: 0.95rem;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 12px 18px;
            margin-bottom: 20px; /* Konsisten margin bottom */
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            color: #333;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #7D5FFF; /* Warna aksen yang mewah */
            box-shadow: 0 0 0 3px rgba(125, 95, 255, 0.15);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        /* Styling untuk placeholder */
        ::placeholder {
          color: #aaa;
          opacity: 1; /* Firefox */
        }
        :-ms-input-placeholder { /* Internet Explorer 10-11 */
         color: #aaa;
        }
        ::-ms-input-placeholder { /* Microsoft Edge */
         color: #aaa;
        }

        .drop-area {
            border: 2px dashed #d0d0d0; /* Warna border lebih lembut */
            border-radius: 10px;
            padding: 25px; /* Padding lebih banyak */
            text-align: center;
            cursor: pointer;
            color: #888;
            background-color: #f9f9f9; /* Latar belakang sedikit berbeda */
            margin-bottom: 20px;
            transition: border-color 0.3s, background-color 0.3s;
        }

        .drop-area.dragover {
            border-color: #7D5FFF; /* Warna aksen saat dragover */
            background-color: #f0edff; /* Warna latar lebih terang saat dragover */
        }

        .drop-area p {
            margin: 0 0 10px 0;
            font-size: 0.9rem;
        }

        #preview {
            margin-top: 15px;
            max-width: 100%; /* Responsif */
            max-height: 200px; /* Batas tinggi */
            border-radius: 8px; /* Border radius konsisten */
            border: 1px solid #eee; /* Border halus */
            object-fit: contain; /* Agar gambar tidak terdistorsi */
            display: none; /* Sembunyikan jika tidak ada src */
        }
        #preview[src]:not([src=""]) { /* Tampilkan jika ada src */
            display: block;
        }


        button[type="submit"] {
            background: linear-gradient(135deg, #7D5FFF 0%, #573EFF 100%); /* Gradient mewah */
            color: white;
            padding: 14px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            font-family: 'Poppins', sans-serif;
            transition: background 0.3s ease, transform 0.2s ease;
            display: block;
            width: 100%; /* Tombol full width */
            margin-top: 10px; /* Sedikit jarak dari elemen terakhir */
        }

        button[type="submit"]:hover {
            background: linear-gradient(135deg, #6a4fe6 0%, #472de0 100%); /* Warna hover lebih gelap */
            transform: translateY(-2px); /* Efek sedikit terangkat */
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        /* Responsiveness: Adjust padding for smaller screens */
        @media (max-width: 768px) {
            .form-tambah {
                padding: 25px 30px;
            }
            .form-tambah h2 {
                font-size: 1.6rem;
                margin-bottom: 25px;
            }
            input[type="text"],
            input[type="number"],
            textarea,
            select,
            .drop-area {
                padding: 10px 15px;
            }
            button[type="submit"] {
                padding: 12px 20px;
            }
        }
    </style>
</head>
<body>

<div class="form-tambah">
    <h2 align="center">Tambah Makanan/Minuman</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="nama">Nama:</label>
        <input type="text" id="nama" name="nama" required placeholder="Contoh: Nasi Goreg Spesial">

        <label for="deskripsi">Deskripsi:</label>
        <textarea id="deskripsi" name="deskripsi" required placeholder="Contoh: Nasi goreng dengan telur, ayam, dan udang"></textarea>

        <label for="harga">Harga:</label>
        <input type="number" id="harga" name="harga" required placeholder="Contoh: 25000">

        <label for="stok">Stok:</label>
        <input type="number" id="stok" name="stok" required placeholder="Contoh: 50">

<div class="drop-area" id="drop-area">
    <label for="gambar-drop">Masukkan gambar</label>
  <p>Drag & drop gambar di sini atau klik untuk memilih</p>
  <input type="file" id="gambar" name="gambar" accept="image/*" hidden required>
  <img id="preview" src="" alt="" style="display:none;" />
</div>

        <label for="kategori_id">Kategori:</label>
        <select id="kategori_id" name="kategori_id" required>
            <option value="">-- Pilih Kategori --</option>
            <?php while($row = mysqli_fetch_assoc($kategori)): ?>
                <option value="<?= htmlspecialchars($row['kategori_id']) ?>"><?= htmlspecialchars($row['nama_kategori']) ?></option>
            <?php endwhile; ?>
        </select>
        
        <button type="submit" name="tambah">Tambah Produk</button>
    </form>
</div>
    <script src="script.js"></script>
</body>
</html>

