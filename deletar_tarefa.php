<?php
include 'conexão.php';

// Lê o corpo da requisição JSON
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['id'])) {
    $id = $input['id'];

    $stmt = $conn->prepare("DELETE FROM tarefas WHERE id_terefas = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'Erro ao deletar a tarefa do banco de dados.']);
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'ID da tarefa não fornecido.']);
}
?>
