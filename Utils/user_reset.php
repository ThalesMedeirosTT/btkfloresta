<?php
header('Content-Type: application/json');
require '../Utils/conexao.php';

$user = $_POST['user'] ?? '';
$capStmt = $pdo->prepare("SELECT resetar FROM alunos WHERE id=?");
$capStmt->execute([$user]);
$resetar = $capStmt->fetchColumn();
if ($resetar === 'S') {
    $aux = $pdo->prepare("UPDATE alunos set resetar = 'N' WHERE id=?");
    $aux->execute([$user]);

    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['success' => false]);