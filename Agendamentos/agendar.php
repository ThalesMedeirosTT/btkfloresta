<?php
header('Content-Type: application/json');
require '../Utils/conexao.php';

$aula_id = $_POST['aula_id'] ?? null;
$aluno_id = $_POST['aluno_id'] ?? null;
$action = $_POST['action'] ?? null;

if (!$aula_id || !$aluno_id) {
    echo json_encode(['success' => false, 'mensagem' => 'Parâmetros ausentes']);
    exit;
}

try {
    $pdo->beginTransaction();

    // ====== BUSCA DADOS DA AULA ======
    $capStmt = $pdo->prepare("SELECT capacidade FROM aulas WHERE id=? FOR UPDATE");
    $capStmt->execute([$aula_id]);
    $capacidade = (int)$capStmt->fetchColumn();

    if (!$capacidade) {
        throw new Exception("Aula não encontrada.");
    }

    // ====== ACTION: AGENDAR ======
    if ($action === 'book') {
        // Verifica se já existe agendamento
        $check = $pdo->prepare("SELECT status FROM agendamentos WHERE aula_id=? AND aluno_id=? FOR UPDATE");
        $check->execute([$aula_id, $aluno_id]);
        if ($check->fetch()) {
            throw new Exception("Você já está inscrito nesta aula.");
        }

        // Conta quantos confirmados
        $cnt = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE aula_id=? AND status='confirmado' FOR UPDATE");
        $cnt->execute([$aula_id]);
        $confirmados = (int)$cnt->fetchColumn();

        // Define status (confirmado ou espera)
        $status = $confirmados >= $capacidade ? 'espera' : 'confirmado';

        $ins = $pdo->prepare("INSERT INTO agendamentos (aluno_id, aula_id, status) VALUES (?,?,?)");
        $ins->execute([$aluno_id, $aula_id, $status]);

        $pdo->commit();

        if ($status == 'espera') {
            echo json_encode([
                'success' => true,
                'mensagem' => 'Você está na lista de espera. Avisaremos se liberar uma vaga!',
                'status' => $status
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'mensagem' => 'Vaga agendada! Bom treino!',
                'status' => $status
            ]);
        }
        exit;
    }

    // ====== ACTION: CANCELAR AGENDAMENTO ======
    elseif ($action === 'cancel') {
        // Remove o aluno
        $del = $pdo->prepare("DELETE FROM agendamentos WHERE aula_id=? AND aluno_id=?");
        $del->execute([$aula_id, $aluno_id]);

        // Verifica lista de espera
        $espera = $pdo->prepare("
            SELECT aluno_id FROM agendamentos 
            WHERE aula_id=? AND status='espera'
            ORDER BY id ASC
            LIMIT 1
            FOR UPDATE
        ");
        $espera->execute([$aula_id]);
        $prox = $espera->fetchColumn();

        // Se há alguém na espera, promove para confirmado
        if ($prox) {
            $upd = $pdo->prepare("UPDATE agendamentos SET status='confirmado' WHERE aula_id=? AND aluno_id=?");
            $upd->execute([$aula_id, $prox]);
        }

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'mensagem' => 'Agendamento removido com sucesso.'
        ]);
        exit;
    }

    // ====== ACTION: CANCELAR ESPERA ======
    elseif ($action === 'cancel_waitlist') {
        $del = $pdo->prepare("DELETE FROM agendamentos WHERE aula_id=? AND aluno_id=? AND status='espera'");
        $del->execute([$aula_id, $aluno_id]);

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'mensagem' => 'Você foi removido da lista de espera.'
        ]);
        exit;
    } else {
        throw new Exception("Ação inválida.");
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'mensagem' => $e->getMessage()]);
}
