<?php
header('Content-Type: application/json');
require '../Utils/adminStatus.php';
require '../Utils/conexao.php';

$data = $_POST['data'];
$hora = $_POST['hora'];
$professor = $_POST['professor'];
$capacidade = $_POST['capacidade'];
$tipo = $_POST['tipo'];
$template_id = $_POST['template_id'];

try {
    $stmt = $pdo->prepare("INSERT INTO aulas (data, hora, professor, capacidade, tipo, template_id) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$data, $hora, $professor, $capacidade, $tipo, $template_id]);
    echo json_encode(['success'=>true,'mensagem'=>'Aula criada.']);
} catch (Exception $e){
    echo json_encode(['success'=>false,'mensagem'=>$e->getMessage()]);
}
?>