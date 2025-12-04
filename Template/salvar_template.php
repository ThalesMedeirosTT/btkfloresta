<?php
header('Content-Type: application/json');
require '../Utils/adminStatus.php';
require '../Utils/conexao.php';

$dia = $_POST['dia'] ?? null;
$hora = $_POST['hora'] ?? null;
$prof = $_POST['professor'] ?? null;
$cap = intval($_POST['capacidade'] ?? 20);
$tipo = $_POST['tipo'] ?? null;
if(!$dia || !$hora || !$prof){ echo json_encode(['success'=>false,'mensagem'=>'Parâmetros ausentes']); exit; }
try {
    $ins = $pdo->prepare("INSERT INTO templates_aulas (dia_semana, hora, professor, capacidade, tipo) VALUES (?,?,?,?,?)");
    $ins->execute([$dia, $hora, $prof, $cap, $tipo]);
    echo json_encode(['success'=>true,'id'=>$pdo->lastInsertId()]);
} catch (Exception $e){
    echo json_encode(['success'=>false,'mensagem'=>$e->getMessage()]);
}
?>