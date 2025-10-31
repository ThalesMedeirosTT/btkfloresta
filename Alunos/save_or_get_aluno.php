<?php
header('Content-Type: application/json');
require '../Utils/conexao.php';
$nome = trim($_POST['nome'] ?? '');
$celular = trim($_POST['celular'] ?? '');
$data = trim($_POST['data'] ?? '');
if($nome === ''){
    echo json_encode(['success'=>false,'message'=>'Nome vazio']);
    exit;
}
try {
    // Check if exists
    $stmt = $pdo->prepare("SELECT id FROM alunos WHERE celular = ? LIMIT 1");
    $stmt->execute([$celular]);
    $row = $stmt->fetch();
    if($row){
        echo json_encode(['success'=>true,'aluno_id'=>$row['id']]);
        exit;
    }
    // Insert
    $ins = $pdo->prepare("INSERT INTO alunos (nome, celular, data_nascimento) VALUES (?, ?, ?)");
    $ins->execute([$nome, $celular, $data]);
    echo json_encode(['success'=>true,'aluno_id'=>$pdo->lastInsertId()]);
} catch (Exception $e){
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>