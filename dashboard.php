
<?php
require_once __DIR__ . "/../config/koneksi.php";

if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: ../auth/login.php");
    exit;
}

function hitungData($koneksi, $sql) {
    $result = $koneksi->query($sql);
    return (int) $result->fetch_assoc()['total'];
}

$totalKontestan = hitungData(
    $koneksi,
    "SELECT COUNT(*) AS total FROM kontestan"
);

$totalPemilih = hitungData(
    $koneksi,
    "SELECT COUNT(*) AS total FROM users WHERE role='pemilih'"
);

$totalKakang = hitungData(
    $koneksi,
    "SELECT COUNT(*) AS total FROM voting WHERE kategori='Kakang'"
);

$totalSenduk = hitungData(
    $koneksi,
    "SELECT COUNT(*) AS total FROM voting WHERE kategori='Senduk'"
);

$periode = $koneksi->query("
    SELECT * FROM periode_voting
    ORDER BY id DESC LIMIT 1
")->fetch_assoc();

$hasil = $koneksi->query("
    SELECT
        k.nama,
        k.nomor_peserta,
        k.kategori,
        COUNT(v.id) AS total_suara
    FROM kontestan k
    LEFT JOIN voting v ON k.id = v.kontestan_id
    GROUP BY k.id
    ORDER BY total_suara DESC
    LIMIT 5
");

$namaAdmin = htmlspecialchars($_SESSION['nama']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Admin | Kakang Senduk</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f3f6f1;
    font-family: Arial, sans-serif;
    color: #344a39;
}

.sidebar {
    position: fixed;
    width: 250px;
    height: 100vh;
    background: #304f39;
    padding: 25px 15px;
    color: white;
}

.sidebar h4 {
    font-size: 19px;
    margin-bottom: 35px;
    padding-left: 10px;
}

.sidebar a {
    display: block;
    padding: 13px 15px;
    color: #e0e9df;
    text-decoration: none;
    border-radius: 8px;
    margin-bottom: 7px;
}

.sidebar a:hover,
.sidebar a.active {
    background: #52785b;
    color: white;
}

.main {
    margin-left: 250px;
    padding: 30px;
}

.topbar {
    background: white;
    padding: 18px 25px;
    border-radius: 12px;
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-card {
    background: white;
    border-radius: 13px;
    padding: 23px;
    border: 1px solid #e8eee5;
    height: 100%;
}

.stat-card p {
    color: #7b897d;
    margin-bottom: 10px;
}

.stat-card h2 {
    font-weight: bold;
    color: #426b4e;
}

.content-card {
    background: white;
    border-radius: 13px;
    padding: 25px;
    margin-top: 25px;
}

.table th {
    color: #526b57;
    background: #f0f4ed;
}

@media(max-width: 768px) {
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
    }

    .main {
        margin-left: 0;
        padding: 15px;
    }
}
</style>
</head>

<body>

<aside class="sidebar">

    <h4>🌿 Kakang Senduk</h4>

    <a href="dashboard.php" class="active">▦ Dashboard</a>
    <a href="kontestan.php">♙ Data Kontestan</a>
    <a href="pemilih.php">♧ Data Pemilih</a>
    <a href="hasil-voting.php">▤ Hasil Voting</a>
    <a href="periode.php">◷ Periode Voting</a>

    <hr>

    <a href="../pages/index.php">Lihat Website</a>
    <a href="../auth/logout.php">Logout</a>

</aside>

<main class="main">

    <div class="topbar">
        <div>
            <h4 class="mb-1">Dashboard Administrator</h4>
            <small class="text-secondary">
                Selamat datang, <?= $namaAdmin ?>
            </small>
        </div>

        <span class="badge bg-success">Administrator</span>
    </div>

    <h5 class="mb-4">Ringkasan Statistik</h5>

    <div class="row g-3">

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <p>Total Kontestan</p>
                <h2><?= $totalKontestan ?></h2>
                <small>Seluruh kandidat</small>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <p>Total Pemilih</p>
                <h2><?= $totalPemilih ?></h2>
                <small>Akun terdaftar</small>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <p>Suara Kakang</p>
                <h2><?= $totalKakang ?></h2>
                <small>Total suara masuk</small>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <p>Suara Senduk</p>
                <h2><?= $totalSenduk ?></h2>
                <small>Total suara masuk</small>
            </div>
        </div>

    </div>

    <div class="content-card">

        <h5 class="mb-3">Status Periode Voting</h5>

        <?php if ($periode): ?>

            <h6><?= htmlspecialchars($periode['nama_periode']) ?></h6>

            <p class="text-secondary">
                Mulai:
                <?= htmlspecialchars($periode['tanggal_mulai']) ?>
            </p>

            <p class="text-secondary">
                Selesai:
                <?= htmlspecialchars($periode['tanggal_selesai']) ?>
            </p>

            <span class="badge bg-success">
                <?= htmlspecialchars($periode['status']) ?>
            </span>

        <?php else: ?>

            <p class="text-secondary">
                Belum ada periode voting.
            </p>

        <?php endif; ?>

    </div>

    <div class="content-card">

        <h5 class="mb-3">5 Kontestan dengan Suara Terbanyak</h5>

        <div class="table-responsive">

            <table class="table table-bordered align-middle">

                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nomor Peserta</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Jumlah Suara</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($hasil->num_rows > 0): ?>

                    <?php $no = 1; ?>

                    <?php while ($row = $hasil->fetch_assoc()): ?>

                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nomor_peserta']) ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['kategori']) ?></td>
                        <td>
                            <strong><?= $row['total_suara'] ?></strong>
                        </td>
                    </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5" class="text-center">
                            Belum ada data kontestan.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>

</body>
</html>
