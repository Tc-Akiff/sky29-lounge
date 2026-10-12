<?php
session_start();
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SKY29 Lounge</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-firestore-compat.js"></script>

    <script>
        const firebaseConfig = {
          apiKey: "AIzaSyC_hESDUgVpm-x_Ld5cE2gfMepz7SiwaGs",
          authDomain: "sky29-lounge.firebaseapp.com",
          projectId: "sky29-lounge",
          storageBucket: "sky29-lounge.firebasestorage.app",
          messagingSenderId: "265145556160",
          appId: "1:265145556160:web:441cb15f46a570695fa125",
          measurementId: "G-HWJBY59SE4"
        };
        
        firebase.initializeApp(firebaseConfig);
        const db = firebase.firestore();
    </script>

    <style>
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; margin: 0; padding: 0; }
        body {
            background-color: #050914;
            background-image: 
                radial-gradient(circle at 50% 20%, rgba(56, 189, 248, 0.2) 0%, transparent 60%),
                linear-gradient(rgba(5, 9, 20, 0.88), rgba(5, 9, 20, 0.95)),
                url('cityscape-of-george-town-2026-03-17-00-01-12-utc.jpg');
            background-position: center; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;
            color: #FFFFFF; min-height: 100vh; padding: 24px 16px;
        }
        .container { max-width: 1100px; margin: 0 auto; }
        .header-card {
            background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px);
            border-radius: 24px; border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 20px 24px; margin-bottom: 24px; display: flex; justify-style: space-between; align-items: center; flex-wrap: wrap; gap: 12px;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.06); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border-radius: 24px; border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 20px; margin-bottom: 28px; box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }
        .table-responsive { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; min-width: 600px; }
        th { padding: 12px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #38BDF8; border-bottom: 1px solid rgba(255, 255, 255, 0.15); }
        td { padding: 14px 16px; font-size: 12px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); vertical-align: middle; }
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 10px; font-weight: 600; display: inline-block; }
        .status-PENDING { background: rgba(245, 158, 11, 0.2); color: #FBBF24; }
        .status-LULUS { background: rgba(52, 211, 153, 0.2); color: #34D399; }
        .status-DIBATALKAN { background: rgba(248, 113, 113, 0.2); color: #F87171; }
        .btn-lulus { padding: 6px 12px; background: #10B981; border:none; color: #fff; border-radius: 10px; font-weight: 600; font-size: 11px; cursor: pointer; }
        .btn-batal { padding: 6px 12px; background: #EF4444; border:none; color: #fff; border-radius: 10px; font-weight: 600; font-size: 11px; cursor: pointer; margin-left: 4px; }
        .btn-logout { padding: 8px 16px; background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); color: #FCA5A5; border-radius: 12px; text-decoration: none; font-size: 12px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <div class="header-card">
            <div>
                <a href="index.php" style="color:#38BDF8; font-size:12px; text-decoration:none;">← Ke Laman Utama</a>
                <h2 style="font-size:20px; margin-top:4px;">Portal Pentadbir SKY29</h2>
            </div>
            <a href="logout.php" class="btn-logout">Log Keluar</a>
        </div>

        <!-- SENARAI PERMOHONAN -->
        <div class="glass-panel">
            <h3 style="font-size:15px; margin-bottom:16px; color:#38BDF8;">📋 Senarai Permohonan Tempahan</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pemohon / Unit</th>
                            <th>Tarikh & Masa</th>
                            <th>Tujuan</th>
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody id="jadual-tempahan">
                        <tr><td colspan="6" style="text-align:center;">Memuatkan data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- AUDIT LOG -->
        <div class="glass-panel">
            <h3 style="font-size:15px; margin-bottom:16px; color:#38BDF8;">📜 Audit Log Aktiviti</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tarikh & Masa</th>
                            <th>ID Tempahan</th>
                            <th>Tindakan Log</th>
                            <th>Pelaksana</th>
                        </tr>
                    </thead>
                    <tbody id="jadual-audit">
                        <tr><td colspan="4" style="text-align:center;">Memuatkan log...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        db.collection("tempahan").orderBy("created_at", "desc").onSnapshot(function(snapshot) {
            var tbody = document.getElementById("jadual-tempahan");
            tbody.innerHTML = "";

            if (snapshot.empty) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Tiada tempahan lagi.</td></tr>';
                return;
            }

            snapshot.forEach(function(doc) {
                var t = doc.data();
                var docId = doc.id;
                var booking_id = "#SKY" + docId.substring(0, 5).toUpperCase();

                var tr = document.createElement("tr");
                tr.innerHTML = 
                    '<td><strong>' + booking_id + '</strong></td>' +
                    '<td><div><strong>' + t.nama + '</strong></div><div style="font-size:10px; color:rgba(255,255,255,0.6);">' + t.bahagian + ' | ' + t.no_tel + '</div></td>' +
                    '<td><div>' + t.tarikh + '</div><div style="font-size:10px; color:rgba(255,255,255,0.6);">' + t.masa_mula + ' - ' + t.masa_tamat + '</div></td>' +
                    '<td>' + t.tujuan + '</td>' +
                    '<td><span class="badge status-' + t.status + '">' + t.status + '</span></td>' +
                    '<td>' +
                        (t.status !== 'LULUS' ? '<button class="btn-lulus" onclick="kemaskiniStatus(\'' + docId + '\', \'LULUS\', \'' + booking_id + '\')">Lulus</button>' : '') +
                        (t.status !== 'DIBATALKAN' ? '<button class="btn-batal" onclick="kemaskiniStatus(\'' + docId + '\', \'DIBATALKAN\', \'' + booking_id + '\')">Batal</button>' : '') +
                    '</td>';
                tbody.appendChild(tr);
            });
        });

        function kemaskiniStatus(docId, statusBaru, bookingId) {
            db.collection("tempahan").doc(docId).update({
                status: statusBaru
            }).then(function() {
                var tindakanText = statusBaru === 'LULUS' ? 'DILULUSKAN_ADMIN' : 'DIBATALKAN_ADMIN';
                db.collection("audit_log").add({
                    tempahan_id: bookingId,
                    tindakan: tindakanText,
                    dilakukan_oleh: "Pentadbir (Admin)",
                    tarikh_masa: new Date().toLocaleString('ms-MY')
                });
            });
        }

        db.collection("audit_log").onSnapshot(function(snapshot) {
            var tbody = document.getElementById("jadual-audit");
            tbody.innerHTML = "";

            if (snapshot.empty) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">Tiada log aktiviti.</td></tr>';
                return;
            }

            snapshot.forEach(function(doc) {
                var log = doc.data();
                var tr = document.createElement("tr");
                tr.innerHTML = 
                    '<td style="font-size:11px; color:rgba(255,255,255,0.7);">' + log.tarikh_masa + '</td>' +
                    '<td><strong>' + log.tempahan_id + '</strong></td>' +
                    '<td><span style="color:#38BDF8; font-weight:600;">' + log.tindakan + '</span></td>' +
                    '<td>' + log.dilakukan_oleh + '</td>';
                tbody.appendChild(tr);
            });
        });
    </script>
</body>
</html>
