<?php
header('Content-Type: application/json');
require '../Utils/conexao.php';

$dia_semana = $_POST['dia_semana'] ?? null;
$hora = $_POST['hora'] ?? null;
try {
    $params = [];

    $sql = "SELECT id, dia_semana, hora, professor, capacidade, tipo, criado_em FROM templates_aulas WHERE 1=1";
    if($dia_semana != null){
        $sql .= " and dia_semana = ?";
        $params[] = $dia_semana;
    }
    if($hora != null){
        $sql .= " and hora = ?";
        $params[] = $hora;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    
    $list = [];
    foreach($rows as $r){
        $list[] = ['id'=>$r['id'],'dia_semana'=>$r['dia_semana'],'hora'=>$r['hora'],'professor'=>$r['professor'],'capacidade'=>$r['capacidade'], 'tipo'=>$r['tipo'],'criado_em'=>$r['criado_em']];
    }
    echo json_encode(['success'=>true,'list'=>$list]);
} catch (Exception $e){
    echo json_encode(['success'=>false,'mensagem'=>$e->getMessage()]);
}
?>