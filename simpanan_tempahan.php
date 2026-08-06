<?php
header('Content-Type: application/json');

try {
    // Sambung ke SQLite
    $db = new PDO('sqlite:perpustakaan.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Cipta jadual automatik jika belum wujud
    $db->exec("CREATE TABLE IF NOT EXISTS tempahan (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nama TEXT,
        bahagian TEXT,
        no_tel TEXT,
        email TEXT,
        tarikh TEXT,
        masa_mula TEXT,
        masa_tamat TEXT,
        tujuan TEXT
    )");

    // Terima data POST
    $nama       = $_POST['nama'] ?? '';
    $bahagian   = $_POST['bahagian'] ?? '';
    $no_tel     = $_POST['no_tel'] ?? '';
    $email      = $_POST['email'] ?? '';
    $tarikh     = $_POST['tarikh'] ?? '';
    $masa_mula  = $_POST['masa_mula'] ?? '';
    $masa_tamat = $_POST['masa_tamat'] ?? '';
    $tujuan     = $_POST['tujuan'] ?? '';

    // Validasi Asas: Semak jika ada medan kosong
    if (empty($nama) || empty($tarikh) || empty($masa_mula) || empty($masa_tamat)) {
        echo json_encode(['status' => 'error', 'message' => 'Sila isi semua maklumat yang diperlukan.']);
        exit;
    }

    // Semak Pertindihan Masa
    $stmt = $db->prepare("SELECT * FROM tempahan WHERE tarikh = :tarikh AND (:masa_mula < masa_tamat AND :masa_tamat > masa_mula)");
    $stmt->execute([
        ':tarikh' => $tarikh,
        ':masa_mula' => $masa_mula,
        ':masa_tamat' => $masa_tamat
    ]);

    if ($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'TEMPAHAN BERTINDIH! Masa ini telah ditempah oleh pengguna lain.']);
        exit;
    }

    // Simpan Tempahan
    $insert = $db->prepare("INSERT INTO tempahan (nama, bahagian, no_tel, email, tarikh, masa_mula, masa_tamat, tujuan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $insert->execute([$nama, $bahagian, $no_tel, $email, $tarikh, $masa_mula, $masa_tamat, $tujuan]);

    echo json_encode(['status' => 'success', 'message' => 'Tempahan berjaya disimpan!']);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Ralat Server: ' . $e->getMessage()]);
}
?>