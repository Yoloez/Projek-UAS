<?php
include '../koneksi.php';

$id = $_GET['food_id'];
$result = mysqli_query($conn, "SELECT * FROM foods WHERE food_id=$id");
$data = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    mysqli_query($conn, "UPDATE menu SET nama='$nama', kategori='$kategori', harga='$harga', stok='$stok' WHERE id=$id");
    header("Location: index.php");
}
?>

<form method="POST">
    <input type="text" name="nama" value="<?= $data['nama'] ?>"><br>
    <select name="kategori">
        <option value="makanan" <?= $data['kategori']=='makanan'?'selected':'' ?>>Makanan</option>
        <option value="minuman" <?= $data['kategori']=='minuman'?'selected':'' ?>>Minuman</option>
    </select><br>
    <input type="number" name="harga" value="<?= $data['harga'] ?>"><br>
    <input type="number" name="stok" value="<?= $data['stok'] ?>"><br>
    <button type="submit" name="update">Update</button>
</form>
