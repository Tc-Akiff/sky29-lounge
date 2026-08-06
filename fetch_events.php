<?php
header('Content-Type: application/json');

try {
    $db = new PDO('sqlite:perpustakaan.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $db->exec("CREATE TABLE IF NOT EXISTS tempahan (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nama TEXT, bahagian TEXT, no_tel TEXT, email TEXT, tarikh TEXT, masa_mula TEXT, masa_tamat TEXT, tujuan TEXT
    )");

    $stmt = $db->query("SELECT * FROM tempahan");
    $events = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $events[] = [
            'id'    => $row['id'],
            'title' => 'TIDAK KOSONG (DITEMPAH)',
            'start' => $row['tarikh'] . 'T' . $row['masa_mula'],
            'end'   => $row['tarikh'] . 'T' . $row['masa_tamat'],
            'color' => '#EF4444' // Warna merah
        ];
    }

    echo json_encode($events);
} catch (Exception $e) {
    echo json_encode([]);
}
?>