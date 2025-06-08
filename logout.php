<?php
session_start();
session_destroy();
header("Location: authentication/login/login.php?pesan=logout_berhasil");
// Redirect ke halaman login dengan pesan logout berhasil
exit();
?>
