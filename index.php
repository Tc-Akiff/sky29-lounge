<?php
// ==========================================
// 1. BACKEND LOGIC (DATABASE & API)
// ==========================================
$db_file = __DIR__ . '/sky29_lounge.sqlite';
try {
    $db = new PDO("sqlite:" . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Cipta jadual automatik jika belum ada
    $db->exec("CREATE TABLE IF NOT EXISTS tempahan (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nama TEXT, bahagian TEXT, no_tel TEXT, email TEXT,
        tarikh TEXT, masa_mula TEXT, masa_tamat TEXT, tujuan TEXT
    )");
} catch (Exception $e) {
    // Jika ralat pangkalan data
}

// API 1: Hantar Data Tempahan
if (isset($_GET['action']) && $_GET['action'] === 'save') {
    header('Content-Type: application/json');
    
    $nama       = $_POST['nama'] ?? '';
    $bahagian   = $_POST['bahagian'] ?? '';
    $no_tel     = $_POST['no_tel'] ?? '';
    $email      = $_POST['email'] ?? '';
    $tarikh     = $_POST['tarikh'] ?? '';
    $masa_mula  = $_POST['masa_mula'] ?? '';
    $masa_tamat = $_POST['masa_tamat'] ?? '';
    $tujuan     = $_POST['tujuan'] ?? '';

    if (empty($nama) || empty($tarikh) || empty($masa_mula) || empty($masa_tamat)) {
        echo json_encode(['status' => 'error', 'message' => 'Sila isi semua maklumat yang diwajibkan.']);
        exit;
    }

    // Semak Pertindihan
    $stmt = $db->prepare("SELECT * FROM tempahan WHERE tarikh = :tarikh AND (:masa_mula < masa_tamat AND :masa_tamat > masa_mula)");
    $stmt->execute([':tarikh' => $tarikh, ':masa_mula' => $masa_mula, ':masa_tamat' => $masa_tamat]);

    if ($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'TEMPAHAN BERTINDIH! Masa ini telah diisi.']);
        exit;
    }

    // Simpan Tempahan
    $insert = $db->prepare("INSERT INTO tempahan (nama, bahagian, no_tel, email, tarikh, masa_mula, masa_tamat, tujuan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $insert->execute([$nama, $bahagian, $no_tel, $email, $tarikh, $masa_mula, $masa_tamat, $tujuan]);

    echo json_encode(['status' => 'success', 'message' => 'Tempahan berjaya disimpan!']);
    exit;
}

// API 2: Tarik Data Kalendar
if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
    header('Content-Type: application/json');
    $stmt = $db->query("SELECT * FROM tempahan");
    $events = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $events[] = [
            'id'    => $row['id'],
            'title' => 'DITEMPAH (' . $row['bahagian'] . ')',
            'start' => $row['tarikh'] . 'T' . $row['masa_mula'],
            'end'   => $row['tarikh'] . 'T' . $row['masa_tamat'],
            'color' => '#EF4444'
        ];
    }
    echo json_encode($events);
    exit;
}
?>

<!-- ==========================================
     2. FRONTEND DISPLAY (HTML, CSS iOS GLASS, JS)
     ========================================== -->
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Tempahan Lounge SKY29</title>
    
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * { box-sizing: border-box; font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif; margin: 0; padding: 0; }

        body {
            background-color: #050914;
            /* Flare Biru Muda Opacity 65% + Background George Town */
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
            padding: 20px;
            min-height: 100vh;
        }

        .container { max-width: 1200px; margin: 0 auto; }

        /* HEADER & KALENDAR LIQUID GLASS iOS */
        .header-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.18) 0%, rgba(56, 189, 248, 0.12) 50%, rgba(255, 255, 255, 0.08) 100%);
            backdrop-filter: blur(35px) saturate(210%);
            -webkit-backdrop-filter: blur(35px) saturate(210%);
            padding: 22px 32px; border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-top: 1px solid rgba(255, 255, 255, 0.55);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6), 0 0 35px rgba(56, 189, 248, 0.3);
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;
        }

        .header-card h2 {
            color: #FFFFFF; font-size: 22px; font-weight: 700;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5), 0 0 18px rgba(56, 189, 248, 0.7);
        }

        .btn-admin {
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.35) 0%, rgba(255, 255, 255, 0.15) 100%);
            backdrop-filter: blur(15px); color: #FFFFFF;
            padding: 10px 22px; border-radius: 16px; text-decoration: none; font-size: 13px; font-weight: 600;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); transition: all 0.25s ease;
        }

        .btn-admin:hover {
            background: rgba(56, 189, 248, 0.85);
            box-shadow: 0 0 20px rgba(56, 189, 248, 0.8);
            transform: translateY(-2px);
        }

        .calendar-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.14) 0%, rgba(255, 255, 255, 0.04) 100%);
            backdrop-filter: blur(35px) saturate(210%);
            -webkit-backdrop-filter: blur(35px) saturate(210%);
            padding: 28px; border-radius: 36px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-top: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.7), 0 0 45px rgba(56, 189, 248, 0.28);
            min-height: 650px;
        }

        /* FullCalendar Custom Style */
        .fc { color: #FFFFFF !important; }
        .fc-toolbar-title { font-weight: 700 !important; text-shadow: 0 0 12px rgba(56, 189, 248, 0.6); }
        .fc-button-primary { 
            background: rgba(56, 189, 248, 0.25) !important; 
            border: 1px solid rgba(255, 255, 255, 0.35) !important; 
            backdrop-filter: blur(10px) !important; 
            border-radius: 12px !important; transition: all 0.2s ease !important;
        }
        .fc-button-primary:hover { background: #38BDF8 !important; box-shadow: 0 0 18px rgba(56, 189, 248, 0.9) !important; }
        .fc-theme-standard td, .fc-theme-standard th { border-color: rgba(255, 255, 255, 0.12) !important; }
        .fc-daygrid-day-number { color: #FFFFFF !important; font-weight: 500; }
        .fc-col-header-cell-cushion { color: #38BDF8 !important; font-weight: 600; }

        /* MODAL KACA iOS */
        .modal {
            display: none; position: fixed; z-index: 100; left: 0; top: 0;
            width: 100%; height: 100%; background: rgba(5, 9, 20, 0.8); backdrop-filter: blur(14px);
        }

        .modal-content {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.18) 0%, rgba(56, 189, 248, 0.08) 50%, rgba(255, 255, 255, 0.08) 100%);
            backdrop-filter: blur(40px) saturate(210%);
            -webkit-backdrop-filter: blur(40px) saturate(210%);
            margin: 3% auto; padding: 35px; width: 90%; max-width: 480px;
            border-radius: 36px; border: 1px solid rgba(255, 255, 255, 0.25);
            border-top: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.8), 0 0 45px rgba(56, 189, 248, 0.35);
            position: relative; color: #FFFFFF;
        }

        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: rgba(255, 255, 255, 0.95); text-shadow: 0 1px 3px rgba(0,0,0,0.5); }
        
        .form-group input { 
            width: 100%; padding: 12px 16px; border: 1px solid rgba(255, 255, 255, 0.25); 
            border-top: 1px solid rgba(255, 255, 255, 0.4); border-radius: 16px; font-size: 13px; outline: none; 
            background: rgba(255, 255, 255, 0.12); color: #FFFFFF; backdrop-filter: blur(15px); transition: all 0.25s ease; 
        }
        .form-group input::placeholder { color: rgba(255, 255, 255, 0.5); }
        .form-group input:focus { background: rgba(255, 255, 255, 0.25); border-color: #38BDF8; box-shadow: 0 0 20px rgba(56, 189, 248, 0.6); }
        .form-row { display: flex; gap: 10px; }

        .btn-submit {
            width: 100%; padding: 14px; background: linear-gradient(135deg, #38BDF8 0%, #0284C7 100%); 
            color: white; border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 18px; font-weight: 600; font-size: 14.5px; cursor: pointer; margin-top: 10px;
            box-shadow: 0 10px 25px -5px rgba(56, 189, 248, 0.6); transition: all 0.25s ease;
        }
        .btn-submit:hover { transform: scale(1.02); box-shadow: 0 14px 30px -5px rgba(56, 189, 248, 0.85); }

        .btn-close-modal {
            position: absolute; top: 20px; right: 20px; background: rgba(255, 255, 255, 0.15); color: #FFFFFF; 
            border: 1px solid rgba(255, 255, 255, 0.3); width: 32px; height: 32px; border-radius: 50%; cursor: pointer; backdrop-filter: blur(10px);
        }
        .btn-close-modal:hover { background: rgba(239, 68, 68, 0.8); }

        /* Modal Resit */
        .receipt-box { background: rgba(255, 255, 255, 0.1); border: 1px dashed rgba(255, 255, 255, 0.3); border-radius: 20px; padding: 18px; margin-bottom: 18px; backdrop-filter: blur(10px); }
        .receipt-item { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px; color: rgba(255,255,255,0.9); }
        .reminder-box { background: rgba(245, 158, 11, 0.2); border-left: 4px solid #F59E0B; padding: 12px; border-radius: 12px; margin-bottom: 18px; font-size: 12px; color: #FDE68A; border: 1px solid rgba(245, 158, 11, 0.3); }
        .btn-print { width: 100%; padding: 14px; background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; border: 1px solid rgba(255,255,255,0.4); border-radius: 18px; font-weight: 600; cursor: pointer; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.4); }
    </style>
</head>
<body>

    <div class="container">
        <!-- Header Utama -->
        <div class="header-card">
            <div>
                <h2>Lounge SKY29</h2>
                <p style="font-size:12px; color:rgba(255,255,255,0.85);">Sistem Tempahan Bulanan</p>
            </div>
            <a href="login.php" class="btn-admin">Portal Admin</a>
        </div>

        <!-- Kalendar Utama -->
        <div class="calendar-card">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Modal Borang Tempahan -->
    <div id="bookingModal" class="modal">
        <div class="modal-content">
            <button class="btn-close-modal" onclick="closeModal('bookingModal')">✕</button>
            <h3 style="margin-bottom:18px; text-shadow:0 0 12px rgba(56, 189, 248, 0.7);">Borang Tempahan Lounge SKY29</h3>
            <form id="bookingForm">
                <div class="form-group">
                    <label>Nama Pemohon</label>
                    <input type="text" id="nama" placeholder="Nama Penuh" required autocomplete="off">
                </div>
                <div class="form-row">
                    <div class="form-group" style="flex:1;">
                        <label>Bahagian/Unit</label>
                        <input type="text" id="bahagian" placeholder="Cth: BKP" required>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>No. Telefon</label>
                        <input type="text" id="no_tel" placeholder="012-XXXXXXX" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>E-mel Pemohon</label>
                    <input type="email" id="email" placeholder="contoh@penang.gov.my" required>
                </div>
                <div class="form-group">
                    <label>Tarikh Dipilih</label>
                    <input type="date" id="tarikh" readonly style="opacity:0.8;">
                </div>
                <div class="form-row">
                    <div class="form-group" style="flex:1;">
                        <label>Masa Mula</label>
                        <input type="time" id="masa_mula" value="09:00" required>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Masa Tamat</label>
                        <input type="time" id="masa_tamat" value="10:00" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Tujuan Tempahan</label>
                    <input type="text" id="tujuan" placeholder="Cth: Perbincangan Program" required>
                </div>
                <button type="submit" class="btn-submit">Hantar Tempahan</button>
            </form>
        </div>
    </div>

    <!-- Modal Resit / Bukti -->
    <div id="receiptModal" class="modal">
        <div class="modal-content">
            <button class="btn-close-modal" onclick="closeModal('receiptModal')">✕</button>
            
            <div style="text-align:center; margin-bottom:18px;">
                <div style="width:55px; height:55px; background:rgba(16, 185, 129, 0.25); color:#34D399; border:1px solid rgba(52, 211, 153, 0.5); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:26px; margin:0 auto 10px auto; box-shadow:0 0 20px rgba(52, 211, 153, 0.4);">✓</div>
                <h3 style="color:#34D399; text-shadow:0 0 10px rgba(52, 211, 153, 0.5);">Tempahan Berjaya!</h3>
            </div>

            <div class="reminder-box">
                📌 <strong>Peringatan:</strong> Mohon simpan bukti tempahan Lounge SKY29 bagi tujuan pembuktian.
            </div>

            <div class="receipt-box">
                <div class="receipt-item"><span>Nama:</span><strong id="res_nama" style="color:#38BDF8;">-</strong></div>
                <div class="receipt-item"><span>Bahagian:</span><strong id="res_bahagian">-</strong></div>
                <div class="receipt-item"><span>Tarikh:</span><strong id="res_tarikh">-</strong></div>
                <div class="receipt-item"><span>Masa:</span><strong id="res_masa">-</strong></div>
                <div class="receipt-item"><span>Tujuan:</span><strong id="res_tujuan">-</strong></div>
            </div>

            <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan Bukti</button>
        </div>
    </div>

    <script>
        let calendar;

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');

            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                height: 'auto',
                headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
                events: 'index.php?action=fetch',

                dateClick: function(info) {
                    document.getElementById('tarikh').value = info.dateStr;
                    document.getElementById('bookingModal').style.display = 'block';
                }
            });

            calendar.render();
        });

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const nama = document.getElementById('nama').value;
            const bahagian = document.getElementById('bahagian').value;
            const no_tel = document.getElementById('no_tel').value;
            const email = document.getElementById('email').value;
            const tarikh = document.getElementById('tarikh').value;
            const masa_mula = document.getElementById('masa_mula').value;
            const masa_tamat = document.getElementById('masa_tamat').value;
            const tujuan = document.getElementById('tujuan').value;

            const formData = new FormData();
            formData.append('nama', nama);
            formData.append('bahagian', bahagian);
            formData.append('no_tel', no_tel);
            formData.append('email', email);
            formData.append('tarikh', tarikh);
            formData.append('masa_mula', masa_mula);
            formData.append('masa_tamat', masa_tamat);
            formData.append('tujuan', tujuan);

            fetch('index.php?action=save', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    closeModal('bookingModal');

                    document.getElementById('res_nama').innerText = nama;
                    document.getElementById('res_bahagian').innerText = bahagian;
                    document.getElementById('res_tarikh').innerText = tarikh;
                    document.getElementById('res_masa').innerText = masa_mula + ' - ' + masa_tamat;
                    document.getElementById('res_tujuan').innerText = tujuan;

                    document.getElementById('receiptModal').style.display = 'block';
                    calendar.refetchEvents();
                    document.getElementById('bookingForm').reset();
                } else {
                    alert('⚠️ ' + data.message);
                }
            });
        });
    </script>
</body>
</html>