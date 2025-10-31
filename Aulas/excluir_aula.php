<?php
header('Content-Type: application/json');
require '../Utils/adminStatus.php';
require '../Utils/conexao.php';

$id = $_POST['id'];

try {
    $del = $pdo->prepare("DELETE FROM agendamentos WHERE aula_id = ?");
    $del->execute([$id]);
    $del = $pdo->prepare("DELETE FROM aulas WHERE id = ?");
    $del->execute([$id]);
    echo json_encode(['success'=>true]);
} catch (Exception $e){
    echo json_encode(['success'=>false,'mensagem'=>$e->getMessage()]);
}
?>