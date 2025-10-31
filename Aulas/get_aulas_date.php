<?php
header('Content-Type: application/json');
require '../Utils/conexao.php';
$data = $_POST['data'] ?? date('Y-m-d');

try {
    $sql = "SELECT id, data, hora, professor, capacidade, template_id, criado_em
    FROM aulas 
    WHERE data = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$data]);
    $rows = $stmt->fetchAll();
    $list = [];
    // normalize day name
    foreach($rows as &$r){
        $list[] = ['id'=>$r['id'],'data'=>$r['data'],'hora'=>$r['hora'],'professor'=>$r['professor'],'capacidade'=>$r['capacidade'],'criado_em'=>$r['criado_em']];
    }
    echo json_encode(['success'=>true,'list'=>$list]);
} catch (Exception $e){
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>