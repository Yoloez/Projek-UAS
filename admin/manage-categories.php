<?php
include 'db.php';
// ... (autentikasi jika ada)

// Proses Tambah Kategori
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    $description = trim($_POST['description']);
    if (!empty($category_name)) {
        $stmt = $conn->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $category_name, $description);
        if ($stmt->execute()) {
            echo "<script>alert('Kategori berhasil ditambahkan.'); window.location.href='manage_categories.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan kategori: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Nama kategori tidak boleh kosong.');</script>";
    }
}

// Proses Hapus Kategori
if (isset($_GET['delete_category'])) {
    $category_id_to_delete = intval($_GET['delete_category']);
    // PERHATIAN: Cek dulu apakah kategori ini digunakan di tabel food
    $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM food WHERE category_id = ?");
    $check_stmt->bind_param("i", $category_id_to_delete);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();

    if ($check_result['count'] > 0) {
        echo "<script>alert('Tidak dapat menghapus kategori karena masih digunakan oleh produk makanan/minuman.'); window.location.href='manage_categories.php';</script>";
    } else {
        $delete_stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
        $delete_stmt->bind_param("i", $category_id_to_delete);
        if ($delete_stmt->execute()) {
            echo "<script>alert('Kategori berhasil dihapus.'); window.location.href='manage_categories.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus kategori: " . $delete_stmt->error . "');</script>";
        }
        $delete_stmt->close();
    }
}


$sql_categories = "SELECT * FROM categories ORDER BY category_name ASC";
$result_categories = $conn->query($sql_categories);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Kategori</title>
    <link rel="stylesheet" href="style_admin.css"> <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f9f9f9; }
        .container { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1, h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #5cb85c; color: white; }
        .action-links a { margin-right: 10px; text-decoration: none; }
        .edit-link { color: #f0ad4e; }
        .delete-link { color: #d9534f; }
        .form-add-category { margin-bottom: 20px; padding: 15px; border: 1px solid #eee; background-color:#fdfdfd; border-radius:5px;}
        .form-add-category label { display: block; margin-bottom: 5px;}
        .form-add-category input[type="text"], .form-add-category textarea { width:98%; padding:8px; margin-bottom:10px; border:1px solid #ccc; border-radius:3px;}
        .form-add-category button { background-color: #5cb85c; color:white; padding:10px 15px; border:none; border-radius:3px; cursor:pointer;}
        .nav-back { margin-bottom: 20px; }
        .nav-back a { text-decoration: none; color: #555; font-weight: bold;}
    </style>
</head>
<body>
    <div class="container">
        <div class="nav-back"><a href="index.php">&laquo; Kembali ke Dashboard</a></div>
        <h1>Kelola Kategori Makanan & Minuman</h1>

        <div class="form-add-category">
            <h2>Tambah Kategori Baru</h2>
            <form action="manage_categories.php" method="post">
                <div>
                    <label for="category_name">Nama Kategori:</label>
                    <input type="text" id="category_name" name="category_name" required>
                </div>
                <div>
                    <label for="description">Deskripsi (Opsional):</label>
                    <textarea id="description" name="description"></textarea>
                </div>
                <button type="submit" name="add_category">Tambah Kategori</button>
            </form>
        </div>

        <h2>Daftar Kategori</h2>
        <?php if ($result_categories && $result_categories->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result_categories->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['category_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row['description'])); ?></td>
                        <td class="action-links">
                            <a href="edit_category.php?id=<?php echo $row['category_id']; ?>" class="edit-link">Edit</a>
                            <a href="manage_categories.php?delete_category=<?php echo $row['category_id']; ?>" class="delete-link" onclick="return confirm('Yakin ingin menghapus kategori ini? Produk yang menggunakan kategori ini mungkin akan terpengaruh.');">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Belum ada data kategori.</p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
$conn->close();
?>