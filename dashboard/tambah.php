<?php
include '../koneksi.php';

// Ambil semua kategori untuk pilihan dropdown
$kategori = mysqli_query($conn, "SELECT * FROM category");

if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $kategori_id = $_POST['kategori_id'];

    // Masukkan data makanan baru ke database
    $query = "INSERT INTO foods (name, description, price, stock, kategori_id) 
              VALUES ('$nama', '$deskripsi', '$harga', '$stok', '$kategori_id')";

    if (mysqli_query($conn, $query)) {
        header("Location: index.php");
        exit();
    } else {
        echo "Gagal menambahkan: " . mysqli_error($conn);
    }
}
?>

<!-- Form HTML -->
<h2>Tambah Makanan/Minuman</h2>
<form action="" method="POST">
    <label>Nama:</label><br>
    <input type="text" name="nama" required><br><br>

    <label>Deskripsi:</label><br>
    <textarea name="deskripsi" required></textarea><br><br>

    <label>Harga:</label><br>
    <input type="number" name="harga" required><br><br>

    <label>Stok:</label><br>
    <input type="number" name="stok" required><br><br>

    <label for="gambar">Input Gambar</label>
    <input type="file" name="gambar" accept="image/*" required><br><br>

    <label>Kategori:</label><br>
    <select name="kategori_id" required>
        <option value="">-- Pilih Kategori --</option>
        <?php while($row = mysqli_fetch_assoc($kategori)): ?>
            <option value="<?= $row['kategori_id'] ?>"><?= $row['nama_kategori'] ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <button type="submit" name="tambah">Tambah</button>
</form>
