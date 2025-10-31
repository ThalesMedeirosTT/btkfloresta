<?php
header('Content-Type: application/json');
session_start();

$key = $_POST['key'] ?? '';
// Keep same admin key as original
if($key === 'Floresta@2025@Muaythai'){
    $_SESSION['is_admin'] = true;
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false]);
}
?>