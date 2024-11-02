<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "survei_gadget");

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Tambah data dengan prepared statement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $gadget = $_POST['gadget'];

    $stmt = $conn->prepare("INSERT INTO gadget_siswa (nama, kelas, gadget) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nama, $kelas, $gadget);

    if ($stmt->execute()) {
        // Redirect ke grafik.php setelah menambah data
        header("Location: grafik.php");
        exit();
    } else {
        echo "Gagal menambah data: " . $stmt->error;
    }

    $stmt->close();
}

// Hapus data
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($conn->query("DELETE FROM gadget_siswa WHERE id=$id") === FALSE) {
        echo "Gagal menghapus data: " . $conn->error;
    }
    header("Location: grafik.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survei Gadget Siswa</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Survei Gadget Siswa</h1>
    
    <!-- Form Survei -->
    <form action="" method="POST" class="survey-form">
    <h2>Form Survei Gadget Siswa</h2>
    <label for="nama">Nama:</label>
    <input type="text" name="nama" id="nama" required placeholder="Masukkan nama Anda">

    <label for="kelas">Kelas:</label>
    <input type="text" name="kelas" id="kelas" required placeholder="Masukkan kelas Anda">

    <label for="gadget">Gadget:</label>
    <select name="gadget" id="gadget" required>
        <option value="" disabled selected>Pilih gadget Anda</option>
        <option value="Smartphone">Smartphone</option>
        <option value="Tablet">Tablet</option>
        <option value="Laptop">Laptop</option>
        <option value="Smartwatch">Smartwatch</option>
        <option value="Others">Others</option>
    </select>

    <button type="submit" name="add" class="submit-button">Tambah</button>
</form>

