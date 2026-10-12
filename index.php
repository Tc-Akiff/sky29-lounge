<?php
$db_file = __DIR__ . '/sky29_lounge.sqlite';
try {
    $db = new PDO("sqlite:" . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $db->exec("CREATE TABLE IF NOT EXISTS tempahan (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nama TEXT, bahagian TEXT, no_tel TEXT, email TEXT,
        tarikh TEXT, masa_mula TEXT, masa_tamat TEXT, tujuan TEXT,
        status TEXT DEFAULT 'PENDING'
    )");
} catch (Exception $e) {
}

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
        echo json_encode(['status' => 'error', 'message' => 'Sila lengkapkan semua maklumat wajib.']);
        exit;
    }

    $stmt = $db->prepare("SELECT * FROM tempahan WHERE tarikh = :tarikh AND (:masa_mula < masa_tamat AND :masa_tamat > masa_mula)");
    $stmt->execute([':tarikh' => $tarikh, ':masa_mula' => $masa_mula, ':masa_tamat' => $masa_tamat]);

    if ($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'SLOT BERTINDIH! Masa ini telah ditempah oleh pengguna lain.']);
        exit;
    }

    $insert = $db->prepare("INSERT INTO tempahan (nama, bahagian, no_tel, email, tarikh, masa_mula, masa_tamat, tujuan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $insert->execute([$nama, $bahagian, $no_tel, $email, $tarikh, $masa_mula, $masa_tamat, $tujuan]);

    $last_id = $db->lastInsertId();
    $booking_id = "#SKY" . str_pad($last_id, 5, '0', STR_PAD_LEFT);

    echo json_encode([
        'status' => 'success', 
        'message' => 'Tempahan berjaya dihantar!',
        'booking_id' => $booking_id
    ]);
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'check_status') {
    header('Content-Type: application/json');
    $query = $_POST['carian'] ?? '';

    $stmt = $db->prepare("SELECT * FROM tempahan WHERE no_tel = :q OR email = :q ORDER BY id DESC");
    $stmt->execute([':q' => $query]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Tempahan Lounge SKY29</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; margin: 0; padding: 0; }
        body {
            background-color: #050914;
            background-image: 
                radial-gradient(circle at 50% 20%, rgba(56, 189, 248, 0.25) 0%, transparent 60%),
                linear-gradient(rgba(5, 9, 20, 0.85), rgba(5, 9, 20, 0.92)),
                url('cityscape-of-george-town-2026-03-17-00-01-12-utc.jpg');
            background-position: center; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;
            color: #FFFFFF; padding-bottom: 90px; min-height: 100vh;
        }
        .app-container { display: flex; flex-direction: column; flex: 1; width: 100%; max-width: 500px; margin: 0 auto; padding: 20px; }
        .hero-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15), rgba(56, 189, 248, 0.1));
            backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px);
            border-radius: 28px; border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 24px; margin-bottom: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }
        .badge-tag {
            background: rgba(56, 189, 248, 0.2); color: #38BDF8; font-size: 11px;
            padding: 5px 12px; border-radius: 20px; font-weight: 600; display: inline-block; margin-bottom: 10px;
        }
        .grid-action { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 24px; }
        .action-card {
            background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(20px);
            border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 18px; text-align: left; cursor: pointer; transition: all 0.2s ease;
        }
        .action-card:hover { background: rgba(56, 189, 248, 0.2); transform: translateY(-2px); }
        .facility-card {
            background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(20px);
            border-radius: 24px; border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 16px; margin-bottom: 16px; display: flex; gap: 14px; align-items: center;
        }
        .facility-img { width: 85px; height: 85px; border-radius: 16px; object-fit: cover; }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 12px; margin-bottom: 6px; color: rgba(255,255,255,0.8); }
        .form-group input {
            width: 100%; padding: 12px 16px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.1); color: #fff; font-size: 13px; outline: none;
        }
        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: rgba(5, 9, 20, 0.85); backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            display: flex; justify-content: space-around; padding: 12px 0; z-index: 1000;
        }
        .nav-item { text-align: center; color: rgba(255, 255, 255, 0.6); font-size: 11px; cursor: pointer; flex: 1; }
        .nav-item.active { color: #38BDF8; font-weight: 600; }
        .nav-item div { font-size: 18px; margin-bottom: 2px; }
        .section-tab { display: none; }
        .section-tab.active { display: block; }
        .btn-primary {
            width: 100%; padding: 14px; background: linear-gradient(135deg, #38BDF8, #0284C7);
            border: none; border-radius: 16px; color: #fff; font-weight: 600; cursor: pointer; margin-top: 12px;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- TAB UTAMA -->
        <div id="tab-utama" class="section-tab active">
            <div class="hero-card">
                <span class="badge-tag">SISTEM PINTAR</span>
                <h2>Selamat Datang,</h2>
                <p style="font-size: 13px; color: rgba(255,255,255,0.7); margin-bottom: 16px;">Sistem Tempahan Lounge SKY29</p>
                <a href="login.php" style="color:#38BDF8; font-size:12px; text-decoration:none;">Portal Admin -></a>
            </div>
            <div class="grid-action">
                <div class="action-card" onclick="switchTab('tab-tempah')">
                    <div style="font-size:24px; margin-bottom:8px;">📅</div>
                    <strong>Tempah Fasiliti</strong>
                    <p style="font-size:11px; color:rgba(255,255,255,0.6);">Mula tempahan baru</p>
                </div>
                <div class="action-card" onclick="switchTab('tab-status')">
                    <div style="font-size:24px; margin-bottom:8px;">🔍</div>
                    <strong>Semak Status</strong>
                    <p style="font-size:11px; color:rgba(255,255,255,0.6);">Rekod tempahan</p>
                </div>
            </div>
            <h4 style="margin-bottom:12px;">Fasiliti Tersedia</h4>
            <div class="facility-card">
                <img src="cityscape-of-george-town-2026-03-17-00-01-12-utc.jpg" class="facility-img" alt="Lounge">
                <div>
                    <span class="badge-tag" style="margin-bottom:4px;">LOUNGE</span>
                    <h4 style="font-size:15px;">Lounge SKY29 Utama</h4>
                    <p style="font-size:11px; color:rgba(255,255,255,0.6);">Kapasiti: 30-50 Orang | Smart TV | Audio</p>
                    <button class="btn-primary" style="padding:6px 12px; font-size:11px; margin-top:8px;" onclick="switchTab('tab-tempah')">Pilih Fasiliti</button>
                </div>
            </div>
        </div>

        <!-- TAB TEMPAH -->
        <div id="tab-tempah" class="section-tab">
            <h3 style="margin-bottom: 16px;">Tempah Lounge SKY29</h3>
            <form id="formTempahan">
                <div class="form-group">
                    <label>1. Pilih Tarikh Tempahan *</label>
                    <input type="date" id="tarikh" required>
                </div>
                <div class="form-group">
                    <label>2. Pilih Masa Mula & Tamat *</label>
                    <div style="display:flex; gap:10px;">
                        <input type="time" id="masa_mula" value="09:00" required>
                        <input type="time" id="masa_tamat" value="10:00" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>3. Nama Pemohon *</label>
                    <input type="text" id="nama" placeholder="Taip nama penuh" required>
                </div>
                <div class="form-group">
                    <label>4. Bahagian / Unit *</label>
                    <input type="text" id="bahagian" placeholder="Cth: BKP / Unit IT" required>
                </div>
                <div class="form-group">
                    <label>5. No. Telefon *</label>
                    <input type="text" id="no_tel" placeholder="012-XXXXXXX" required>
                </div>
                <div class="form-group">
                    <label>6. E-mel *</label>
                    <input type="email" id="email" placeholder="contoh@penang.gov.my" required>
                </div>
                <div class="form-group">
                    <label>7. Tujuan Tempahan *</label>
                    <input type="text" id="tujuan" placeholder="Cth: Mesyuarat Khas" required>
                </div>
                <button type="submit" class="btn-primary">Hantar Tempahan</button>
            </form>
        </div>

        <!-- TAB STATUS -->
        <div id="tab-status" class="section-tab">
            <h3 style="margin-bottom: 16px;">Semak Status Tempahan</h3>
            <p style="font-size: 12px; color: rgba(255,255,255,0.7); margin-bottom: 16px;">Masukkan No. Telefon atau E-mel yang digunakan semasa tempahan.</p>
            <div class="form-group">
                <input type="text" id="carian_status" placeholder="Masukkan No. Tel atau E-mel">
                <button class="btn-primary" onclick="semakStatus()">Semak Sekarang</button>
            </div>
            <div id="keputusan-status" style="margin-top:20px;"></div>
        </div>
    </div>

    <!-- BOTTOM NAV -->
    <div class="bottom-nav">
        <div class="nav-item active" onclick="switchTab('tab-utama', this)">
            <div>🏠</div>Utama
        </div>
        <div class="nav-item" onclick="switchTab('tab-tempah', this)">
            <div>📅</div>Tempah
        </div>
        <div class="nav-item" onclick="switchTab('tab-status', this)">
            <div>📑</div>Status
        </div>
    </div>

    <script>
        function switchTab(tabId, el) {
            var tabs = document.querySelectorAll('.section-tab');
            for(var i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            document.getElementById(tabId).classList.add('active');

            if(el) {
                var navs = document.querySelectorAll('.nav-item');
                for(var j = 0; j < navs.length; j++) {
                    navs[j].classList.remove('active');
                }
                el.classList.add('active');
            }
        }

        document.getElementById('formTempahan').addEventListener('submit', function(e) {
            e.preventDefault();
            var formData = new FormData();
            formData.append('nama', document.getElementById('nama').value);
            formData.append('bahagian', document.getElementById('bahagian').value);
            formData.append('no_tel', document.getElementById('no_tel').value);
            formData.append('email', document.getElementById('email').value);
            formData.append('tarikh', document.getElementById('tarikh').value);
            formData.append('masa_mula', document.getElementById('masa_mula').value);
            formData.append('masa_tamat', document.getElementById('masa_tamat').value);
            formData.append('tujuan', document.getElementById('tujuan').value);

            fetch('index.php?action=save', { method: 'POST', body: formData })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.status === 'success') {
                    alert('Tempahan Berjaya Dihantar! ID Tempahan Anda: ' + data.booking_id);
                    document.getElementById('formTempahan').reset();
                    switchTab('tab-status');
                } else {
                    alert(data.message);
                }
            });
        });

        function semakStatus() {
            var input = document.getElementById('carian_status').value;
            if(!input) { alert('Sila masukkan No. Tel atau E-mel'); return; }
            var formData = new FormData();
            formData.append('carian', input);

            fetch('index.php?action=check_status', { method: 'POST', body: formData })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                var container = document.getElementById('keputusan-status');
                container.innerHTML = '';
                if(data.length === 0) {
                    container.innerHTML = '<p style="font-size:12px; color:rgba(255,255,255,0.6);">Tiada rekod dijumpai.</p>';
                    return;
                }
                for(var k = 0; k < data.length; k++) {
                    var item = data[k];
                    var itemBox = document.createElement('div');
                    itemBox.className = 'facility-card';
                    itemBox.style.flexDirection = 'column';
                    itemBox.style.alignItems = 'flex-start';
                    itemBox.innerHTML = 
                        '<div style="display:flex; justify-content:space-between; width:100%;">' +
                            '<strong>#SKY' + String(item.id).padStart(5, '0') + '</strong>' +
                            '<span class="badge-tag" style="background:rgba(245, 158, 11, 0.2); color:#FBBF24;">' + item.status + '</span>' +
                        '</div>' +
                        '<p style="font-size:12px; margin-top:6px;">Tarikh: ' + item.tarikh + ' (' + item.masa_mula + ' - ' + item.masa_tamat + ')</p>' +
                        '<p style="font-size:11px; color:rgba(255,255,255,0.6);">Tujuan: ' + item.tujuan + '</p>';
                    container.appendChild(itemBox);
                }
            });
        }
    </script>
</body>
</html>
