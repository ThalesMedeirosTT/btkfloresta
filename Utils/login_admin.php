<?php
header('Content-Type: application/json');
require '../Utils/conexao.php';
session_start();

$key = $_POST['key'] ?? '';
$user = $_POST['user'] ?? '';
// Keep same admin key as original
if($key === 'Floresta@2025@Muaythai'){
    $_SESSION['is_admin'] = true;
    echo json_encode(['success'=>true]);
} else {
    if(!empty($user)){
        $capStmt = $pdo->prepare("SELECT tipo FROM alunos WHERE id=?");
        $capStmt->execute([$user]);
        $tipo = $capStmt->fetchColumn();

        if($tipo === 'A'){
            $_SESSION['is_admin'] = true;
            echo json_encode(['success'=>true]);
            exit;
        }
    }

    echo json_encode(['success'=>false]);
}
?>