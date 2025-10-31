<?php
header('Content-Type: application/json');
require '../Utils/conexao.php';
$aula_id = $_GET['aula_id'] ?? null;
if(!$aula_id){ echo json_encode(['success'=>false,'mensagem'=>'aula_id ausente']); exit; }
try {
    $stmt = $pdo->prepare("SELECT al.id, al.nome, ag.status FROM agendamentos ag JOIN alunos al ON al.id = ag.aluno_id WHERE ag.aula_id = ? ORDER BY ag.criado_em");
    $stmt->execute([$aula_id]);
    $rows = $stmt->fetchAll();
    $confirmed = []; $wait = [];
    foreach($rows as $r){
        if($r['status'] === 'confirmado') $confirmed[] = ['id'=>$r['id'],'nome'=>$r['nome']];
        else $wait[] = ['id'=>$r['id'],'nome'=>$r['nome']];
    }
    echo json_encode(['success'=>true,'confirmed'=>$confirmed,'waitlist'=>$wait]);
} catch (Exception $e){
    echo json_encode(['success'=>false,'mensagem'=>$e->getMessage()]);
}
?>