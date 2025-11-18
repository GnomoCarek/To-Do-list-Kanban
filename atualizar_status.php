<?php
include 'conexão.php';

// Lê o corpo da requisição JSON
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['id']) && isset($input['status'])) {
    $id = $input['id'];
    $status = $input['status'];

    // Validação simples do status
    $status_permitidos = ['fazer', 'fazendo', 'concluido'];
    if (!in_array($status, $status_permitidos)) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Status inválido.']);
        exit();
    }

    $stmt = $conn->prepare("UPDATE tarefas SET status_tarefa = ? WHERE id_tarefas = ?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar o banco de dados.']);
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'ID da tarefa ou novo status não fornecido.']);
}
?>
