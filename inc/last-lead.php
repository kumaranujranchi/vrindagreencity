<?php
// Returns the last inserted lead in contact_leads for quick verification
header('Content-Type: application/json');

require_once __DIR__ . '/../admin/config.php';

try {
    $conn = getDBConnection();
    $sql = "SELECT * FROM contact_leads ORDER BY id DESC LIMIT 1";
    $res = dbQuery($conn, $sql);
    if (!$res['success']) {
        throw new Exception('Query failed: ' . ($res['error'] ?? 'unknown'));
    }
    $rows = $res['rows'];
    $row = (!empty($rows)) ? $rows[0] : null;
    closeDBConnection($conn);

    if ($row) {
        echo json_encode(['type' => 'success', 'lead' => $row]);
    } else {
        echo json_encode(['type' => 'empty', 'message' => 'No leads found']);
    }
} catch (Exception $e) {
    echo json_encode(['type' => 'error', 'message' => $e->getMessage()]);
}
?>