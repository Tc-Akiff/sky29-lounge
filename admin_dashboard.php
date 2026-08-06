<?php
session_start();

// Semakan Akses Admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Sambung ke SQLite
$db_file = __DIR__ . '/sky29_lounge.sqlite';
$db = new PDO("sqlite:" . $db_file);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Proses Padam Rekod
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $db->prepare("DELETE FROM tempahan WHERE id = ?");
    $stmt->execute([$id]);

    // Semak jika pangkalan data dah kosong sepenuhnya, reset urutan SQLite
    $check = $db->query("SELECT COUNT(*) FROM tempahan")->fetchColumn();
    if ($check == 0) {
        $db->exec("DELETE FROM sqlite_sequence WHERE name='tempahan'");
    }

    $bulan_param = $_GET['bulan'] ?? date('Y-m');
    header("Location: admin_dashboard.php?bulan=" . $bulan_param);
    exit;
}

// Tangkap Bulan Pilihan (Default: Bulan & Tahun Semasa)
$selected_month = $_GET['bulan'] ?? date('Y-m');

// Tarik Rekod Mengikut Bulan Dipilih
$stmt = $db->prepare("SELECT * FROM tempahan WHERE strftime('%Y-%m', tarikh) = :bulan ORDER BY tarikh ASC, masa_mula ASC");
$stmt->execute([':bulan' => $selected_month]);
$tempahan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Tempahan Lounge SKY29</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif; margin: 0; padding: 0; }

        body {
            background-color: #050914;
            background-image: 
                radial-gradient(circle at 50% 30%, rgba(56, 189, 248, 0.29) 0%, rgba(14, 165, 233, 0.08) 40%, transparent 70%),
                radial-gradient(circle at 10% 80%, rgba(56, 189, 248, 0.19) 0%, transparent 45%),
                linear-gradient(rgba(5, 9, 20, 0.72), rgba(5, 9, 20, 0.82)),
                url('cityscape-of-george-town-2026-03-17-00-01-12-utc.jpg');
            background-position: center center;
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
            color: #FFFFFF;
            padding: 30px 20px;
            min-height: 100vh;
        }

        .container { max-width: 1200px; margin: 0 auto; }

        /* HEADER KACA iOS */
        .header { 
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.18) 0%, rgba(56, 189, 248, 0.12) 50%, rgba(255, 255, 255, 0.08) 100%);
            backdrop-filter: blur(35px) saturate(210%);
            -webkit-backdrop-filter: blur(35px) saturate(210%);
            padding: 22px 32px; border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-top: 1px solid rgba(255, 255, 255, 0.55);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6), 0 0 35px rgba(56, 189, 248, 0.3);
        }

        .header h2 {
            color: #FFFFFF; font-size: 22px; font-weight: 700;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5), 0 0 18px rgba(56, 189, 248, 0.7);
        }

        .btn-logout { 
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%); 
            color: white; padding: 10px 18px; text-decoration: none; border-radius: 14px; font-size: 13px; font-weight: 600;
            border: 1px solid rgba(255, 255, 255, 0.4); box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4); transition: all 0.25s ease;
        }
        .btn-logout:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(239, 68, 68, 0.6); }

        .btn-back { 
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.35) 0%, rgba(255, 255, 255, 0.15) 100%); 
            color: white; padding: 10px 18px; text-decoration: none; border-radius: 14px; font-size: 13px; font-weight: 600; 
            margin-right: 10px; border: 1px solid rgba(255, 255, 255, 0.4); backdrop-filter: blur(15px); transition: all 0.25s ease;
        }
        .btn-back:hover { background: rgba(56, 189, 248, 0.85); transform: translateY(-2px); box-shadow: 0 0 20px rgba(56, 189, 248, 0.8); }

        /* KAD PENAPIS BULAN & BUTTON EXCEL KACA iOS */
        .filter-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.16) 0%, rgba(56, 189, 248, 0.08) 100%);
            backdrop-filter: blur(35px) saturate(210%);
            -webkit-backdrop-filter: blur(35px) saturate(210%);
            padding: 20px 28px; border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.22); border-top: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5), 0 0 30px rgba(56, 189, 248, 0.25);
            margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;
        }

        .filter-group { display: flex; align-items: center; gap: 12px; }
        .filter-group label { font-size: 14px; color: #FFFFFF; text-shadow: 0 1px 3px rgba(0,0,0,0.5); }
        .filter-group input { 
            padding: 10px 16px; border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 14px; outline: none; 
            background: rgba(255, 255, 255, 0.15); color: #FFFFFF; backdrop-filter: blur(15px); font-size: 13px;
        }
        .filter-group input::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; }

        .btn-export { 
            background: linear-gradient(135deg, #10B981 0%, #059669 100%); 
            color: white; padding: 11px 20px; border-radius: 16px; text-decoration: none; font-size: 13px; font-weight: 600; 
            border: 1px solid rgba(255, 255, 255, 0.4); cursor: pointer; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4); transition: all 0.25s ease;
        }
        .btn-export:hover { transform: scale(1.02); box-shadow: 0 12px 25px rgba(16, 185, 129, 0.6); }

        /* JADUAL KACA iOS */
        .table-responsive {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(40px) saturate(210%);
            -webkit-backdrop-filter: blur(40px) saturate(210%);
            border-radius: 28px; border: 1px solid rgba(255, 255, 255, 0.22);
            border-top: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.7), 0 0 40px rgba(56, 189, 248, 0.25);
            overflow: hidden;
        }

        table { width: 100%; border-collapse: collapse; text-align: left; color: #FFFFFF; }
        th, td { padding: 16px 20px; font-size: 13.5px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        th { 
            background: rgba(56, 189, 248, 0.25); color: #38BDF8; font-weight: 600; 
            text-transform: uppercase; letter-spacing: 0.5px; font-size: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        tr:hover { background: rgba(255, 255, 255, 0.08); }
        td strong { color: #FFFFFF; font-weight: 600; }

        .btn-delete { 
            color: #FCA5A5; background: rgba(239, 68, 68, 0.25); border: 1px solid rgba(239, 68, 68, 0.4);
            padding: 6px 14px; border-radius: 10px; font-weight: 600; text-decoration: none; font-size: 12px;
            transition: all 0.2s ease; display: inline-block;
        }
        .btn-delete:hover { background: rgba(239, 68, 68, 0.85); color: #FFFFFF; box-shadow: 0 0 12px rgba(239, 68, 68, 0.6); }
    </style>
</head>
<body>

    <div class="container">
        <!-- Header Dashboard -->
        <div class="header">
            <h2>Dashboard Admin - Tempahan Lounge SKY29</h2>
            <div>
                <a href="index.php" class="btn-back">Lihat Kalendar</a>
                <a href="logout.php" class="btn-logout">Log Keluar</a>
            </div>
        </div>

        <!-- Kad Penapis Bulan & Butang Excel -->
        <div class="filter-card">
            <form method="GET" action="admin_dashboard.php" class="filter-group">
                <label><strong>Pilih Bulan:</strong></label>
                <input type="month" name="bulan" value="<?= htmlspecialchars($selected_month) ?>" onchange="this.form.submit()">
            </form>

            <button class="btn-export" onclick="exportTableToExcel('jadual-tempahan', 'Tempahan_SKY29_<?= $selected_month ?>')">
                📥 Eksport Bulan Ini ke Excel
            </button>
        </div>

        <!-- Jadual Rekod Dalam Bekas Kaca -->
        <div class="table-responsive">
            <table id="jadual-tempahan">
                <thead>
                    <tr>
                        <th>Bil.</th>
                        <th>Tarikh</th>
                        <th>Masa Mula</th>
                        <th>Masa Tamat</th>
                        <th>Nama Pemohon</th>
                        <th>Bahagian</th>
                        <th>No. Tel</th>
                        <th>E-mel</th>
                        <th>Tujuan</th>
                        <th class="no-export">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tempahan_list)): ?>
                        <tr><td colspan="10" style="text-align:center; color:rgba(255,255,255,0.7); padding:30px;">Tiada rekod tempahan untuk bulan ini (<?= htmlspecialchars($selected_month) ?>).</td></tr>
                    <?php else: ?>
                        <?php $bil = 1; ?>
                        <?php foreach($tempahan_list as $row): ?>
                        <tr>
                            <td><?= $bil++ ?></td>
                            <td><?= $row['tarikh'] ?></td>
                            <td><?= $row['masa_mula'] ?></td>
                            <td><?= $row['masa_tamat'] ?></td>
                            <td><strong><?= htmlspecialchars($row['nama']) ?></strong></td>
                            <td><?= htmlspecialchars($row['bahagian']) ?></td>
                            <td>'<?= htmlspecialchars($row['no_tel']) ?></td>
                            <td><?= htmlspecialchars($row['email'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['tujuan']) ?></td>
                            <td class="no-export">
                                <a href="admin_dashboard.php?delete=<?= $row['id'] ?>&bulan=<?= $selected_month ?>" class="btn-delete" onclick="return confirm('Padam tempahan ini?')">Padam</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Skrip Eksport Excel -->
    <script>
        function exportTableToExcel(tableID, filename = ''){
            let downloadLink;
            const dataType = 'application/vnd.ms-excel';
            const tableSelect = document.getElementById(tableID);
            
            let tableHTML = tableSelect.outerHTML;

            // Bersihkan kolum "Tindakan" semasa eksport
            tableHTML = tableHTML.replace(/<th class="no-export">.*?<\/th>/gi, '');
            tableHTML = tableHTML.replace(/<td class="no-export">.*?<\/td>/gi, '');

            filename = filename ? filename + '.xls' : 'rekod_tempahan_sky29.xls';
            
            downloadLink = document.createElement("a");
            document.body.appendChild(downloadLink);
            
            if(navigator.msSaveOrOpenBlob){
                var blob = new Blob(['\ufeff' + tableHTML], { type: dataType });
                navigator.msSaveOrOpenBlob( blob, filename);
            } else {
                downloadLink.href = 'data:' + dataType + ', ' + encodeURIComponent(tableHTML);
                downloadLink.download = filename;
                downloadLink.click();
            }
        }
    </script>

</body>
</html>