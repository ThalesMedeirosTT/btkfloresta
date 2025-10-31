<?php
header('Content-Type: application/json');
require '../Utils/conexao.php';
$data = $_GET['data'] ?? date('Y-m-d');

try {
    $sql = "SELECT a.id, a.data, a.hora, a.professor, a.capacidade,
    COALESCE(SUM(CASE WHEN ag.status='confirmado' THEN 1 ELSE 0 END),0) AS confirmed_count,
    COALESCE(SUM(CASE WHEN ag.status='espera' THEN 1 ELSE 0 END),0) AS wait_count
    FROM aulas a
    LEFT JOIN agendamentos ag ON ag.aula_id = a.id
    WHERE a.data = ?
    GROUP BY a.id
    ORDER BY a.hora
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$data]);
    $rows = $stmt->fetchAll();

    $dias = ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'];

    foreach($rows as &$r){
        $indice = date('w', strtotime($r['data'])); 
        $r['data'] = $dias[$indice]; 
    }
    echo json_encode($rows);
} catch (Exception $e){
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>