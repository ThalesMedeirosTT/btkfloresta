<?php
header('Content-Type: application/json');
require '../Utils/adminStatus.php';
require '../Utils/conexao.php';

$id = $_POST['id'] ?? null;
if(!$id){ echo json_encode(['success'=>false,'mensagem'=>'id ausente']); exit; }
try {
    $d = $pdo->prepare("DELETE FROM aulas WHERE template_id = ?");
    $d->execute([$id]);
    $del = $pdo->prepare("DELETE FROM templates_aulas WHERE id = ?");
    $del->execute([$id]);
    echo json_encode(['success'=>true]);
} catch (Exception $e){
    echo json_encode(['success'=>false,'mensagem'=>$e->getMessage()]);
}
?>