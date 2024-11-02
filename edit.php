<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "survei_gadget");

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Cek jika id ada dalam URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Ambil data berdasarkan ID
    $result = $conn->query("SELECT * FROM gadget_siswa WHERE id=$id");
    $data = $result->fetch_assoc();
} else {
    die("ID tidak valid");
}

// Proses pembaruan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $gadget = $_POST['gadget'];

    // Update data
    $sql = "UPDATE gadget_siswa SET nama='$nama', kelas='$kelas', gadget='$gadget' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        header("Location: grafik.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Siswa</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Edit Data Siswa</h1>
    <form method="POST" action="">
        <label for="nama">Nama:</label>
        <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>
        
        <label for="kelas">Kelas:</label>
        <input type="text" id="kelas" name="kelas" value="<?= htmlspecialchars($data['kelas']) ?>" required>
        
        <label for="gadget">Gadget:</label>
        <input type="text" id="gadget" name="gadget" value="<?= htmlspecialchars($data['gadget']) ?>" required>
        
        <button type="submit">Update</button>
        <a href="grafik.php">Batal</a>
    </form>
</body>
</html>
