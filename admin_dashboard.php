<?php
$db_file = __DIR__ . '/sky29_lounge.sqlite';
try {
    $db = new PDO("sqlite:" . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Database Error");
}

// Tindakan Admin: LULUS ATAU BATAL
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $act = $_GET['action'];

    if ($act === 'lulus') {
        $db->prepare("UPDATE tempahan SET status = 'LULUS' WHERE id = ?")->execute([$id]);
        $db->prepare("INSERT INTO audit_log (tempahan_id, tindakan, dilakukan_oleh) VALUES (?, ?, ?)")->execute([$id, 'DILULUSKAN_ADMIN', 'Pentadbir (Admin)']);
    } elseif ($act === 'batal') {
        $db->prepare("UPDATE tempahan SET status = 'DIBATALKAN' WHERE id = ?")->execute([$id]);
        $db->prepare("INSERT INTO audit_log (tempahan_id, tindakan, dilakukan_oleh) VALUES (?, ?, ?)")->execute([$id, 'DIBATALKAN_ADMIN', 'Pentadbir (Admin)']);
    }
    header("Location: admin_dashboard.php");
    exit;
}

// Ambil senarai tempahan
$tempahan_list = $db->query("SELECT * FROM tempahan ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Ambil Audit Log
$audit_logs = $db->query("SELECT a.*, t.nama FROM audit_log a LEFT JOIN tempahan t ON a.tempahan_id = t.id ORDER BY a.id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Lounge SKY29</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; margin:0; padding:0; }
        body { background: #0A0F1D; color: #fff; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        h2, h3 { margin-bottom: 16px; color: #38BDF8; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; background: rgba(255,255,255,0.05); border-radius: 12px; overflow: hidden; }
        th, td { padding: 12px; text-align: left; font-size: 12px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        th { background: rgba(56, 189, 248, 0.2); color: #38BDF8; }
        .btn-lulus { padding: 6px 10px; background: #10B981; color: #fff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 11px; }
        .btn-batal { padding: 6px 10px; background: #EF4444; color: #fff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 11px; margin-left: 4px; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 10px; font-weight: 600; }
        .status-PENDING { background: rgba(245, 158, 11, 0.2); color: #FBBF24; }
        .status-LULUS { background: rgba(52, 211, 153, 0.2); color: #34D399; }
        .status-DIBATALKAN { background: rgba(248, 113, 113, 0.2); color: #F87171; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Portal Admin Lounge SKY29</h2>
        <a href="index.php" style="color: #38BDF8; font-size: 12px; text-decoration: none; display: inline-block; margin-bottom: 20px;">← Kembali ke Halaman Utama</a>

        <h3>Senarai Permohonan Tempahan</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pemohon</th>
                    <th>Tarikh & Masa</th>
                    <th>Tujuan</th>
                    <th>Status</th>
                    <th>Tindakan Admin</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($tempahan_list as $t): ?>
                <tr>
                    <td>#SKY<?= str_pad($t['id'], 5, '0', STR_PAD_LEFT) ?></td>
                    <td><?= htmlspecialchars($t['nama']) ?><br><small style="color:rgba(255,255,255,0.5)"><?= htmlspecialchars($t['no_tel']) ?></small></td>
                    <td><?= htmlspecialchars($t['tarikh']) ?><br><small style="color:rgba(255,255,255,0.5)"><?= $t['masa_mula'] ?> - <?= $t['masa_tamat'] ?></small></td>
                    <td><?= htmlspecialchars($t['tujuan']) ?></td>
                    <td><span class="badge status-<?= $t['status'] ?>"><?= $t['status'] ?></span></td>
                    <td>
                        <?php if($t['status'] !== 'LULUS'): ?>
                            <a href="admin_dashboard.php?action=lulus&id=<?= $t['id'] ?>" class="btn-lulus">Lulus</a>
                        <?php endif; ?>
                        <?php if($t['status'] !== 'DIBATALKAN'): ?>
                            <a href="admin_dashboard.php?action=batal&id=<?= $t['id'] ?>" class="btn-batal">Batal</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>📜 Audit Log Aktiviti</h3>
        <table>
            <thead>
                <tr>
                    <th>Tarikh / Masa</th>
                    <th>ID Tempahan</th>
                    <th>Tindakan Log</th>
                    <th>Pelaksana</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($audit_logs as $log): ?>
                <tr>
                    <td><?= $log['tarikh_masa'] ?></td>
                    <td>#SKY<?= str_pad($log['tempahan_id'], 5, '0', STR_PAD_LEFT) ?></td>
                    <td><b><?= $log['tindakan'] ?></b></td>
                    <td><?= htmlspecialchars($log['dilakukan_oleh']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
