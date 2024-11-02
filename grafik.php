<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "survei_gadget");

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
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

// Pagination settings
$items_per_page = 5; // Jumlah item per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Halaman saat ini
$offset = ($page - 1) * $items_per_page; // Menghitung offset untuk SQL

// Menghitung total data
$total_result = $conn->query("SELECT COUNT(*) as total FROM gadget_siswa");
$total_row = $total_result->fetch_assoc();
$total_items = $total_row['total'];
$total_pages = ceil($total_items / $items_per_page); // Menghitung jumlah halaman

// Ambil data untuk tabel dan grafik
$result = $conn->query("SELECT * FROM gadget_siswa LIMIT $items_per_page OFFSET $offset");
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Survei Gadget</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
    <script src="chart.js"></script> 
    <style>
        /* CSS untuk halaman */
        html, body {
            background-color: #fff; /* Latar belakang putih */
            font-family: 'Helvetica Neue', Helvetica, sans-serif;
            color: rgba(0, 0, 0, 0.75);
        }

        h1 {
            margin: 20px auto;
            text-align: center;
            font-size: 36px;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
        }

        /* CSS untuk grafik */
        .chart-container {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }

        canvas {
            max-width: 400px; /* Ukuran maksimal canvas */
            height: auto; /* Tinggi otomatis */
            margin: 10px auto; /* Margin di atas dan bawah */
        }

        /* CSS untuk tabel */
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }

        th {
            background-color: #f2f2f2;
        }

        a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
        }

        /* CSS untuk pagination */
        .pagination {
            text-align: center;
            margin: 20px auto;
        }

        .pagination a {
            margin: 0 5px;
            padding: 8px 12px;
            border: 1px solid #007bff;
            color: #007bff;
            text-decoration: none;
            border-radius: 5px;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
        }

        .pagination a:hover {
            background-color: #0056b3;
            color: white;
        }
    </style>
</head>
<body>
    <h1>Hasil Survei Gadget Siswa</h1>

    <!-- Tabel Data -->
    <h2>Data Siswa</h2>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Gadget</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['kelas']) ?></td>
                    <td><?= htmlspecialchars($row['gadget']) ?></td>
                    <td>
                        <a href="?delete=<?= $row['id'] ?>">Hapus</a>
                        <a href="edit.php?id=<?= $row['id'] ?>">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Grafik Chart.js -->
    <h2>Grafik Survei</h2>
    <div class="chart-container">
        <canvas id="barChart" width="400" height="400"></canvas>
        <canvas id="pieChart" width="400" height="400"></canvas>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>">Previous</a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?>">Next</a>
        <?php endif; ?>
    </div>

    <script>
        // Data untuk grafik Chart.js
        const data = <?= json_encode($data) ?>;

        // Menghitung jumlah gadget per jenis
        const gadgetCounts = {};
        data.forEach(item => {
            gadgetCounts[item.gadget] = (gadgetCounts[item.gadget] || 0) + 1;
        });

        const gadgets = Object.keys(gadgetCounts);
        const counts = Object.values(gadgetCounts);
        
        // Grafik Batang Chart.js
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: gadgets,
                datasets: [{
                    label: 'Jumlah Siswa per Gadget',
                    data: counts,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });

        // Grafik Pie Chart.js
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: gadgets,
                datasets: [{
                    data: counts,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 159, 64, 0.6)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
    </script>
</body>
</html>
