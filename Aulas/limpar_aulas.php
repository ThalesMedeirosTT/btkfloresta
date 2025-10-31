<?php
header('Content-Type: application/json');
require '../Utils/adminStatus.php';
require '../Utils/conexao.php';

try {
    date_default_timezone_set('America/Sao_Paulo');
    $today = date('Y-m-d');
    $del = $pdo->prepare("DELETE FROM aulas WHERE data < ?");
    $del->execute([$today]);
    echo json_encode(['success'=>true]);
} catch (Exception $e){
    echo json_encode(['success'=>false,'mensagem'=>$e->getMessage()]);
}
?>