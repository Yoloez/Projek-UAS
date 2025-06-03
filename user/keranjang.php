<?php
session_start();
include '../koneksi.php';

// Hapus item dari keranjang
if (isset($_GET['hapus'])) {
    $hapusId = $_GET['hapus'];
    unset($_SESSION['cart'][$hapusId]);
    echo "<script>alert('Item dihapus dari keranjang.');</script>";
    echo "<script>window.location.href = 'keranjang.php';</script>";
    exit;
}

// Ambil ID produk dari keranjang
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$ids = array_keys($cart);

$produk = [];
if (!empty($ids)) {
    $idList = implode(',', $ids);
    $result = mysqli_query($conn, "SELECT * FROM foods WHERE food_id IN ($idList)");
    while ($row = mysqli_fetch_assoc($result)) {
        $produk[$row['food_id']] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Keranjang Belanja</title>
  <style>
    table {
      width: 80%;
      border-collapse: collapse;
      margin: auto;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: center;
    }
    img {
      width: 80px;
      height: 80px;
      object-fit: cover;
    }
  </style>
</head>
<body>
  <h1 style="text-align:center;">Keranjang Belanja</h1>

  <?php if (empty($cart)): ?>
    <p style="text-align:center;">Keranjang kamu kosong. <a href="katalog.php">Kembali ke katalog</a></p>
  <?php else: ?>
    <table>
      <tr>
        <th>Gambar</th>
        <th>Nama</th>
        <th>Harga</th>
        <th>Jumlah</th>
        <th>Subtotal</th>
        <th>Aksi</th>
      </tr>
      <?php
      $total = 0;
      foreach ($cart as $food_id => $jumlah):
        $item = $produk[$food_id];
        $subtotal = $item['price'] * $jumlah;
        $total += $subtotal;
      ?>
      <tr>
        <td><img src="../uploads/<?= $item['image_url'] ?>" alt="<?= $item['name'] ?>"></td>
        <td><?= $item['name'] ?></td>
        <td>Rp <?= number_format($item['price']) ?></td>
        <td><?= $jumlah ?></td>
        <td>Rp <?= number_format($subtotal) ?></td>
        <td><a href="?hapus=<?= $food_id ?>" onclick="return confirm('Hapus dari keranjang?')">Hapus</a></td>
      </tr>
      <?php endforeach; ?>
      <tr>
        <td colspan="4"><strong>Total</strong></td>
        <td colspan="2"><strong>Rp <?= number_format($total) ?></strong></td>
      </tr>
    </table>
    <br>
    <div style="text-align:center;">
      <a href="index.php">← Kembali ke katalog</a> |
      <a href="checkout.php">Checkout</a> <!-- opsional -->
    </div>
  <?php endif; ?>
</body>
</html>
