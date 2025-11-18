<?php
include 'conexão.php';

// --- Lógica de Criação de Tarefa (INSERT) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['descricao_tarefa'])) {
    $descricao = $_POST['descricao_tarefa'];
    $stmt = $conn->prepare("INSERT INTO tarefas (descricao_tarefa, status_tarefa) VALUES (?, 'fazer')");
    $stmt->bind_param("s", $descricao);
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Erro ao criar tarefa: " . $stmt->error;
    }
    $stmt->close();
}

// --- Lógica de Leitura de Tarefas (SELECT) ---
$fazer = [];
$fazendo = [];
$concluido = [];
$sql = "SELECT id_tarefas, descricao_tarefa, status_tarefa FROM tarefas ORDER BY data_tarefa DESC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        switch ($row['status_tarefa']) {
            case 'fazer':
                $fazer[] = $row;
                break;
            case 'fazendo':
                $fazendo[] = $row;
                break;
            case 'concluido':
                $concluido[] = $row;
                break;
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SortableJS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
   
</head>
    <main class="kanban">
        <div class="text-center max-w-lg mx-auto mb-12 p-4">
            <h2 class="text-4xl text-blue-500 mb-4 font-bold m-8">Kanban</h2>
            <form method="POST" action="index.php" class="flex flex-col items-center">
                <input name="descricao_tarefa" class="w-full p-2 border-b-2 border-blue-500 focus:border-blue-00 outline-none mt-4" type="text" placeholder="Digite uma tarefa" required>
                <button class="mt-4 px-4 py-2 border border-blue-500 text-blue-500 rounded-full hover:bg-blue-700 hover:text-white transition duration-300" type="submit">Adicionar</button>
            </form>
        </div>

        <div class="flex flex-col md:flex-row space-y-6 md:space-y-0 md:space-x-6 justify-center">
            <!-- Coluna Fazer -->
            <div class="bg-blue-200 p-4 rounded-lg w-full max-w-sm min-h-80">
                <h3 class="font-bold text-xl text-center text-gray-800 mb-4">Fazer</h3>
                <div id="fazer" class="flex flex-col gap-4 max-h-80 overflow-y-auto">
                    <?php foreach ($fazer as $tarefa): ?>
                        <div data-id="<?= $tarefa['id_tarefas'] ?>" class="task-card bg-white p-3 rounded-lg shadow">
                            <p class="task-description cursor-move"><?= htmlspecialchars($tarefa['descricao_tarefa']) ?></p>
                            <div class="task-actions mt-2 text-right">
                                <button class="edit-btn mt-2 text-sm bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">editar</button>
                                <button class="delete-btn mt-2 text-sm bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">excluir</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Coluna Fazendo -->
            <div class="bg-yellow-200 p-4 rounded-lg w-full max-w-sm min-h-80">
                <h3 class="font-bold text-xl text-center text-gray-800 mb-4">Fazendo</h3>
                <div id="fazendo" class="flex flex-col gap-4 max-h-80 overflow-y-auto">
                    <?php foreach ($fazendo as $tarefa): ?>
                        <div data-id="<?= $tarefa['id_tarefas'] ?>" class="task-card bg-white p-3 rounded-lg shadow">
                            <p class="task-description cursor-move"><?= htmlspecialchars($tarefa['descricao_tarefa']) ?></p>
                            <div class="task-actions mt-2 text-right">
                                <button class="edit-btn mt-2 text-sm bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">editar</button>
                                <button class="delete-btn mt-2 text-sm bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">excluir</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Coluna Concluído -->
            <div class="bg-green-200 p-4 rounded-lg w-full max-w-sm min-h-80">
                <h3 class="font-bold text-xl text-center text-gray-800 mb-4">Concluído</h3>
                <div id="concluido" class="flex flex-col gap-4 max-h-80 overflow-y-auto">
                    <?php foreach ($concluido as $tarefa): ?>
                        <div data-id="<?= $tarefa['id_tarefas'] ?>" class="task-card bg-white p-3 rounded-lg shadow">
                            <p class="task-description cursor-move"><?= htmlspecialchars($tarefa['descricao_tarefa']) ?></p>
                            <div class="task-actions mt-2 text-right">
                                <button class="edit-btn mt-2 text-sm bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">editar</button>
                                <button class="delete-btn mt-2 text-sm bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">excluir</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
     <!-- Seu script customizado -->
    <script src="script.js" defer></script>
</body>
</html>
