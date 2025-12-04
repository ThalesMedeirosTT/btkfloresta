<?php
header('Content-Type: application/json');
require '../Utils/conexao.php';
$nome = trim($_POST['nome'] ?? '');
$celular = trim($_POST['celular'] ?? '');
$data = trim($_POST['data'] ?? '');
if ($nome === '') {
    echo json_encode(['success' => false, 'message' => 'Nome vazio']);
    exit;
}
try {
    // Check if exists
    $stmt = $pdo->prepare("SELECT id, nome FROM alunos WHERE celular = ? LIMIT 1");
    $stmt->execute([$celular]);
    $row = $stmt->fetch();
    if ($row) {
        echo json_encode(['success' => true, 'aluno_id' => $row['id'], 'nome' => $row['nome']]);
        exit;
    }

    function generate_uuid_v4()
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // versão 4
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // variante
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    // Exemplo de uso
    $uuid = generate_uuid_v4();

    // Insert
    $ins = $pdo->prepare("INSERT INTO alunos (id, nome, celular, data_nascimento) VALUES (?, ?, ?, ?)");
    $ins->execute([$uuid, $nome, $celular, $data]);
    echo json_encode(['success' => true, 'aluno_id' => $uuid, 'nome' => $nome]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
