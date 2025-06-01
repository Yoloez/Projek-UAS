<?php
include '../koneksi.php';

$id = $_GET['food_id'];

// Ambil data makanan yang akan diedit
$result = mysqli_query($conn, "SELECT * FROM foods WHERE food_id = $id");
$data = mysqli_fetch_assoc($result);

// Ambil semua kategori untuk dropdown
$kategori = mysqli_query($conn, "SELECT * FROM category");

if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $kategori_id = $_POST['kategori_id'];

    // Update data makanan
    $query = "UPDATE foods SET 
                name = '$nama', 
                description = '$deskripsi', 
                price = '$harga', 
                stock = '$stok',
                kategori_id = '$kategori_id' 
              WHERE food_id = $id";

    if (mysqli_query($conn, $query)) {
        header("Location: index.php");
        exit();
    } else {
        echo "Gagal update: " . mysqli_error($conn);
    }
}
?>

<!-- HTML Form -->
<h2>Edit Makanan</h2>
<form action="" method="POST">
    <label>Nama:</label><br>
    <input type="text" name="nama" value="<?= $data['name'] ?>"><br><br>

    <label>Deskripsi:</label><br>
    <textarea name="deskripsi"><?= $data['description'] ?></textarea><br><br>

    <label>Harga:</label><br>
    <input type="number" name="harga" value="<?= $data['price'] ?>"><br><br>

    <label>Stok:</label><br>
    <input type="number" name="stok" value="<?= $data['stock'] ?>"><br><br>

    <label>Kategori:</label><br>
    <select name="kategori_id">
        <?php while($row = mysqli_fetch_assoc($kategori)): ?>
            <option value="<?= $row['kategori_id'] ?>" 
                <?= $row['kategori_id'] == $data['kategori_id'] ? 'selected' : '' ?>>
                <?= $row['nama_kategori'] ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>

    <button type="submit" name="update">Simpan Perubahan</button>
</form>
