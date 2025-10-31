<?php
header('Content-Type: application/json');
require '../Utils/conexao.php';

try {
    $ids = json_decode($_POST['ids'], true); // array de IDs
    $result = [];

    if ($ids) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT id, aluno_id, aula_id, status, criado_em FROM agendamentos WHERE aula_id IN ($placeholders)");
        $stmt->execute($ids);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agrupa por aula_id
        foreach ($ids as $id) {
            $result[$id] = [];
        }
        foreach ($rows as $row) {
            $result[$row['aula_id']][] = $row;
        }
    }

    echo json_encode(['success' => true, 'list' => $result]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
