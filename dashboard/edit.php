<?php
include '../koneksi.php';

// Pastikan food_id ada dan merupakan integer
if (isset($_GET['food_id']) && filter_var($_GET['food_id'], FILTER_VALIDATE_INT)) {
    $id = (int)$_GET['food_id'];
} else {
    echo "ID makanan tidak valid.";
    exit();
}

// Ambil data makanan yang akan diedit, termasuk image_url
$stmt = mysqli_prepare($conn, "SELECT food_id, name, description, price, stock, kategori_id, image_url FROM foods WHERE food_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    echo "Data makanan tidak ditemukan.";
    exit();
}
$current_image_url = $data['image_url']; // Simpan URL gambar saat ini

// Ambil semua kategori untuk dropdown
$kategori_result = mysqli_query($conn, "SELECT kategori_id, nama_kategori FROM category");
$kategori_options = [];
if ($kategori_result) {
    while($row = mysqli_fetch_assoc($kategori_result)) {
        $kategori_options[] = $row;
    }
}


if (isset($_POST['update'])) {
    $nama = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
    $harga = filter_var(trim($_POST['harga']), FILTER_VALIDATE_FLOAT);
    $stok = filter_var(trim($_POST['stok']), FILTER_VALIDATE_INT);
    $kategori_id = filter_var(trim($_POST['kategori_id']), FILTER_VALIDATE_INT);

    $new_image_filename = $current_image_url; // Default ke gambar saat ini

    // Proses upload gambar baru jika ada
    if (isset($_FILES['gambar_baru']) && $_FILES['gambar_baru']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES['gambar_baru']['tmp_name'];
        $file_name = basename($_FILES['gambar_baru']['name']);
        $file_size = $_FILES['gambar_baru']['size'];
        $file_type = $_FILES['gambar_baru']['type'];
        $file_name_parts = explode(".", $file_name);
        $file_extension = strtolower(end($file_name_parts));

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $target_dir = '../uploads/';

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $sanitized_file_name = preg_replace("/[^A-Za-z0-9\.\-\_]/", "", $file_name); // Sanitasi nama file
        $new_image_filename_potential = time() . '_' . $sanitized_file_name; // Buat nama file unik
        $dest_path = $target_dir . $new_image_filename_potential;

        if (in_array($file_extension, $allowed_extensions)) {
            if ($file_size < 5000000) { // Batas ukuran file 5MB
                if (move_uploaded_file($file_tmp_path, $dest_path)) {
                    // Hapus gambar lama jika ada dan berbeda dari yang baru diupload
                    if (!empty($current_image_url) && $current_image_url !== $new_image_filename_potential && file_exists($target_dir . $current_image_url)) {
                        unlink($target_dir . $current_image_url);
                    }
                    $new_image_filename = $new_image_filename_potential; // Update dengan nama file baru
                } else {
                    echo "Gagal memindahkan file yang diunggah.";
                    // Tetap gunakan gambar lama jika gagal upload
                }
            } else {
                echo "Ukuran file terlalu besar. Maksimal 5MB.";
            }
        } else {
            echo "Tipe file tidak diizinkan. Hanya JPG, JPEG, PNG, GIF.";
        }
    }
    // else: tidak ada file baru diunggah, $new_image_filename tetap $current_image_url

    // Update data makanan dengan prepared statement
    $query_update = "UPDATE foods SET
                name = ?,
                description = ?,
                price = ?,
                stock = ?,
                kategori_id = ?,
                image_url = ?
              WHERE food_id = ?";
    $stmt_update = mysqli_prepare($conn, $query_update);
    mysqli_stmt_bind_param($stmt_update, "ssdiisi", $nama, $deskripsi, $harga, $stok, $kategori_id, $new_image_filename, $id);

    if (mysqli_stmt_execute($stmt_update)) {
        mysqli_stmt_close($stmt_update);
        header("Location: index.php?status=updated");
        exit();
    } else {
        echo "Gagal update: " . mysqli_stmt_error($stmt_update);
        mysqli_stmt_close($stmt_update);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Food/Drink</title>
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
            padding: 20px;
        }

        .form-container {
            background-color: #ffffff;
            padding: 35px 45px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
            width: 100%;
            max-width: 650px; /* Sedikit lebih lebar untuk mengakomodasi preview gambar */
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-container h2 {
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
            margin-bottom: 20px;
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
            border-color: #7D5FFF;
            box-shadow: 0 0 0 3px rgba(125, 95, 255, 0.15);
        }

        textarea {
            min-height: 100px; /* Disesuaikan agar tidak terlalu tinggi */
            resize: vertical;
        }
        
        ::placeholder { color: #aaa; opacity: 1; }
        :-ms-input-placeholder { color: #aaa; }
        ::-ms-input-placeholder { color: #aaa; }

        .drop-area {
            border: 2px dashed #d0d0d0;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            cursor: pointer;
            color: #888;
            background-color: #f9f9f9;
            margin-bottom: 20px;
            transition: border-color 0.3s, background-color 0.3s;
        }

        .drop-area.dragover {
            border-color: #7D5FFF;
            background-color: #f0edff;
        }

        .drop-area p {
            margin: 0 0 10px 0;
            font-size: 0.9rem;
        }

        #preview {
            margin-top: 15px;
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            border: 1px solid #eee;
            object-fit: contain;
            display: none; /* Sembunyikan default, tampilkan via JS jika ada src */
        }
        #preview[src]:not([src="#"]):not([src=""]) { /* Tampilkan jika src valid */
            display: block;
        }


        button[type="submit"] {
            background: linear-gradient(135deg, #7D5FFF 0%, #573EFF 100%);
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
            width: 100%;
            margin-top: 10px;
        }

        button[type="submit"]:hover {
            background: linear-gradient(135deg, #6a4fe6 0%, #472de0 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            .form-container { padding: 25px 30px; }
            .form-container h2 { font-size: 1.6rem; margin-bottom: 25px; }
            input[type="text"], input[type="number"], textarea, select, .drop-area { padding: 10px 15px; }
            button[type="submit"] { padding: 12px 20px; }
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Makanan/Minuman</h2>
    <form action="" method="POST" enctype="multipart/form-data"> <label for="nama">Nama:</label>
        <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($data['name'] ?? '') ?>" required placeholder="Nama Makanan/Minuman">

        <label for="deskripsi">Deskripsi:</label>
        <textarea id="deskripsi" name="deskripsi" required placeholder="Deskripsi produk"><?= htmlspecialchars($data['description'] ?? '') ?></textarea>

        <label for="harga">Harga:</label>
        <input type="number" id="harga" name="harga" value="<?= htmlspecialchars($data['price'] ?? '') ?>" required placeholder="Contoh: 25000" min="0">

        <label for="stok">Stok:</label>
        <input type="number" id="stok" name="stok" value="<?= htmlspecialchars($data['stock'] ?? '') ?>" required placeholder="Contoh: 50" min="0">

        <label for="gambar-input">Gambar Produk (Opsional: Ganti Gambar):</label>
        <div class="drop-area" id="drop-area">
            <p>Seret & lepas gambar baru di sini, atau klik untuk memilih</p>
            <input type="file" id="gambar-input" name="gambar_baru" accept="image/*" hidden>
            <img id="preview" 
                 src="<?= !empty($current_image_url) ? '../uploads/' . htmlspecialchars($current_image_url) : '#' ?>" 
                 alt="Pratinjau Gambar"/>
        </div>

        <label for="kategori_id">Kategori:</label>
        <select id="kategori_id" name="kategori_id" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach($kategori_options as $row): ?>
                <option value="<?= htmlspecialchars($row['kategori_id']) ?>" 
                    <?= ($row['kategori_id'] == ($data['kategori_id'] ?? null)) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['nama_kategori']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <button type="submit" name="update">Simpan Perubahan</button>
    </form>
</div>

<script>
const dropArea = document.getElementById('drop-area');
const inputFile = document.getElementById('gambar-input'); // ID dari input file
const previewImage = document.getElementById('preview');
const initialImageSrc = previewImage.src; // Simpan src awal gambar

// Tampilkan preview jika src awal valid (bukan placeholder '#')
if (initialImageSrc && initialImageSrc !== '#' && !initialImageSrc.endsWith('#')) { // Cek lebih ketat
    previewImage.style.display = 'block';
} else {
    previewImage.style.display = 'none'; // Sembunyikan jika tidak ada gambar awal
}


dropArea.addEventListener('click', () => {
    inputFile.click();
});

inputFile.addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewImage.style.display = 'block';
        }
        reader.readAsDataURL(file);
        dropArea.querySelector('p').textContent = file.name;
    } else {
        // Jika tidak ada file dipilih (misal, pengguna membatalkan), kembalikan ke gambar awal jika ada
        // atau sembunyikan jika tidak ada gambar awal
        if (initialImageSrc && initialImageSrc !== '#' && !initialImageSrc.endsWith('#')) {
            previewImage.src = initialImageSrc;
            previewImage.style.display = 'block';
        } else {
            previewImage.src = '#'; // Reset src
            previewImage.style.display = 'none';
        }
        dropArea.querySelector('p').textContent = 'Seret & lepas gambar baru di sini, atau klik untuk memilih';
    }
});

dropArea.addEventListener('dragover', (event) => {
    event.preventDefault();
    dropArea.classList.add('dragover');
});

dropArea.addEventListener('dragleave', () => {
    dropArea.classList.remove('dragover');
});

dropArea.addEventListener('drop', (event) => {
    event.preventDefault();
    dropArea.classList.remove('dragover');
    
    const files = event.dataTransfer.files;
    if (files.length > 0) {
        inputFile.files = files; // Assign dropped files ke input file
        // Memicu event 'change' secara manual agar logika preview berjalan
        const changeEvent = new Event('change');
        inputFile.dispatchEvent(changeEvent);
    }
});
</script>

</body>
</html>