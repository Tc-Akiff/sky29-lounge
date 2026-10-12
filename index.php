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

echo "API Backend SKY29 Berjaya Beroperasi Tanpa Ralat!";
?>
