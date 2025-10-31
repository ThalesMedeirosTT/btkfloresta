<?php
header('Content-Type: application/json');
session_start();

$key = $_POST['key'] ?? '';
// Keep same admin key as original
if($key === 'admin'){
    $_SESSION['is_admin'] = true;
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false]);
}
?>