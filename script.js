document.addEventListener('DOMContentLoaded', function () {
    const colunas = ['fazer', 'fazendo', 'concluido'];
    const kanbanBoard = document.querySelector('.kanban');

    // 1. Inicializa o SortableJS para cada coluna
    colunas.forEach(idColuna => {
        const el = document.getElementById(idColuna);
        if (!el) return;

        new Sortable(el, {
            group: 'kanban',
            handle: '.task-description',
            animation: 150,
            forceFallback: true,
            onEnd: function (evt) {
                const item = evt.item;
                const idTarefa = item.dataset.id;
                const novoStatus = evt.to.id;

                fetch('atualizar_status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: idTarefa, status: novoStatus }),
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) console.error('Falha ao atualizar o status.');
                })
                .catch(error => console.error('Erro na requisição:', error));
            }
        });
    });

    // 2. Adiciona um ÚNICO listener de clique no board inteiro para as ações
    kanbanBoard.addEventListener('click', function(event) {
        const target = event.target;
        const cardElement = target.closest('.task-card');
        
        // Se o clique não foi dentro de um card, não faz nada
        if (!cardElement) return;

        const idTarefa = cardElement.dataset.id;

        // Ação de Deletar
        if (target.classList.contains('delete-btn')) {
            if (confirm('Tem certeza que deseja deletar esta tarefa?')) {
                fetch('deletar_tarefa.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: idTarefa }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) cardElement.remove();
                    else console.error('Falha ao deletar a tarefa:', data.message);
                })
                .catch(error => console.error('Erro na requisição de deleção:', error));
            }
        }

        // Ação de Editar
        if (target.classList.contains('edit-btn')) {
            const descriptionP = cardElement.querySelector('.task-description');
            const actionsDiv = cardElement.querySelector('.task-actions');
            
            if (cardElement.querySelector('.edit-container')) return;

            const currentText = descriptionP.textContent;
            
            const editContainer = document.createElement('div');
            editContainer.className = 'edit-container mt-2';
            editContainer.innerHTML = `
                <input type="text" class="w-full p-1 border rounded" value="${currentText}">
                <button class="save-btn mt-2 text-sm bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600">Salvar</button>
            `;
            
            descriptionP.style.display = 'none';
            actionsDiv.style.display = 'none';
            cardElement.appendChild(editContainer);

            const saveBtn = editContainer.querySelector('.save-btn');
            const input = editContainer.querySelector('input');
            input.focus();

            const saveChanges = () => {
                const newDescription = input.value;
                
                fetch('editar_tarefa.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: idTarefa, descricao: newDescription }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        descriptionP.textContent = newDescription;
                        cardElement.removeChild(editContainer);
                        descriptionP.style.display = 'block';
                        actionsDiv.style.display = 'block';
                    } else {
                        alert('Erro: ' + (data.message || 'Não foi possível salvar.'));
                    }
                })
                .catch(error => console.error('Erro na requisição de edição:', error));
            };

            saveBtn.addEventListener('click', saveChanges);
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') saveChanges();
            });
        }
    });
});