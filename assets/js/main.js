// /assets/js/main.js

document.addEventListener('DOMContentLoaded', function () {
    // --- SELETORES DE ELEMENTOS ---
    const apiBaseUrl = 'api/contratos/';
    const tabelaContratosBody = document.querySelector('#tabela-contratos tbody');
    const formNovoContrato = document.getElementById('form-novo-contrato');
    const mensagemDiv = document.getElementById('mensagem');

    // Elementos do Modal de Edição
    const editModal = document.getElementById('editModal');
    const closeButton = document.querySelector('.close-button');
    const formEditarContrato = document.getElementById('form-editar-contrato');
    const editMensagemDiv = document.getElementById('edit-mensagem');

    // --- FUNÇÕES PRINCIPAIS ---

    /**
     * Busca os contratos da API e os exibe na tabela.
     */
    async function carregarContratos() {
        // ... (esta função continua exatamente a mesma da resposta anterior)
        try {
            const response = await fetch(`${apiBaseUrl}listar.php`);
            if (!response.ok) throw new Error('Erro de rede: ' + response.statusText);
            const contratos = await response.json();
            tabelaContratosBody.innerHTML = '';
            if (contratos.length === 0) {
                tabelaContratosBody.innerHTML = `<tr><td colspan="7" style="text-align: center;">Nenhum contrato encontrado.</td></tr>`;
                return;
            }
            contratos.forEach(contrato => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${contrato.numero_contrato}</td>
                    <td>${contrato.entidade_nome}</td>
                    <td>${contrato.solucao_nome}</td>
                    <td>${new Date(contrato.data_inicio).toLocaleDateString('pt-BR', { timeZone: 'UTC' })}</td>
                    <td>${new Date(contrato.data_vencimento).toLocaleDateString('pt-BR', { timeZone: 'UTC' })}</td>
                    <td>${contrato.caminho_anexo_pdf ? `<a href="${contrato.caminho_anexo_pdf}" target="_blank">Ver PDF</a>` : 'Nenhum'}</td>
                    <td>
                        <button class="action-btn btn-edit" data-id="${contrato.id}" title="Editar Contrato"><i class="fas fa-pencil-alt"></i></button>
                        <button class="action-btn btn-delete" data-id="${contrato.id}" title="Excluir Contrato"><i class="fas fa-trash-alt"></i></button>
                    </td>
                `;
                tabelaContratosBody.appendChild(tr);
            });
        } catch (error) {
            tabelaContratosBody.innerHTML = `<tr><td colspan="7" style="text-align: center;">Erro ao carregar contratos: ${error.message}</td></tr>`;
        }
    }

    /**
     * Envia os dados do formulário para criar um novo contrato.
     */
    async function criarNovoContrato(e) {
        // ... (esta função continua exatamente a mesma da resposta anterior)
        e.preventDefault();
        const formData = new FormData(formNovoContrato);
        mensagemDiv.textContent = 'Enviando...';
        mensagemDiv.style.color = '#333';
        try {
            const response = await fetch(`${apiBaseUrl}criar.php`, { method: 'POST', body: formData });
            const result = await response.json();
            if (response.ok && result.status === 'success') {
                mensagemDiv.textContent = result.message;
                mensagemDiv.style.color = 'green';
                formNovoContrato.reset();
                carregarContratos();
            } else {
                throw new Error(result.message || 'Ocorreu um erro desconhecido.');
            }
        } catch (error) {
            mensagemDiv.textContent = `Erro: ${error.message}`;
            mensagemDiv.style.color = 'red';
        }
    }

    /**
     * Deleta um contrato baseado no ID fornecido.
     */
    async function deletarContrato(id) {
        // ... (esta função continua exatamente a mesma da resposta anterior)
        if (!confirm('Tem certeza de que deseja excluir este contrato?')) return;
        try {
            const formData = new FormData();
            formData.append('id', id);
            const response = await fetch(`${apiBaseUrl}deletar.php`, { method: 'POST', body: formData });
            const result = await response.json();
            if (response.ok && result.status === 'success') {
                alert(result.message);
                carregarContratos();
            } else {
                throw new Error(result.message || 'Ocorreu um erro ao excluir.');
            }
        } catch (error) {
            alert('Erro: ' + error.message);
        }
    }

    /**
     * Busca dados de um contrato e abre o modal de edição.
     * @param {string} id - O ID do contrato a ser editado.
     */
    async function abrirModalDeEdicao(id) {
        try {
            const response = await fetch(`${apiBaseUrl}obter.php?id=${id}`);
            if (!response.ok) throw new Error('Não foi possível buscar os dados do contrato.');

            const contrato = await response.json();

            // Preenche o formulário do modal com os dados
            document.getElementById('edit-contrato-id').value = contrato.id;
            document.getElementById('edit-numero-contrato').value = contrato.numero_contrato;
            document.getElementById('edit-entidade-id').value = contrato.entidade_id;
            document.getElementById('edit-solucao-id').value = contrato.solucao_id;
            document.getElementById('edit-data-inicio').value = contrato.data_inicio;
            document.getElementById('edit-data-vencimento').value = contrato.data_vencimento;
            document.getElementById('edit-valor-mensal').value = contrato.valor_mensal;

            editMensagemDiv.textContent = ''; // Limpa mensagens de erro antigas
            editModal.style.display = 'block'; // Mostra o modal

        } catch (error) {
            alert('Erro: ' + error.message);
        }
    }

    /**
     * Envia os dados do formulário de edição para a API.
     */
    async function salvarAlteracoesContrato(e) {
        e.preventDefault();
        const formData = new FormData(formEditarContrato);
        editMensagemDiv.textContent = 'Salvando...';
        editMensagemDiv.style.color = '#333';

        try {
            const response = await fetch(`${apiBaseUrl}atualizar.php`, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (response.ok && result.status === 'success') {
                editMensagemDiv.textContent = result.message;
                editMensagemDiv.style.color = 'green';
                setTimeout(() => {
                    editModal.style.display = 'none';
                    carregarContratos();
                }, 1500); // Fecha o modal após 1.5s
            } else {
                throw new Error(result.message || 'Erro ao salvar alterações.');
            }
        } catch (error) {
            editMensagemDiv.textContent = 'Erro: ' + error.message;
            editMensagemDiv.style.color = 'red';
        }
    }

    // --- EVENT LISTENERS ---

    formNovoContrato.addEventListener('submit', criarNovoContrato);
    formEditarContrato.addEventListener('submit', salvarAlteracoesContrato);

    tabelaContratosBody.addEventListener('click', function (e) {
        const target = e.target.closest('.action-btn');
        if (!target) return;

        const id = target.dataset.id;
        if (target.classList.contains('btn-delete')) {
            deletarContrato(id);
        }
        if (target.classList.contains('btn-edit')) {
            abrirModalDeEdicao(id);
        }
    });

    // Listeners para fechar o modal
    closeButton.addEventListener('click', () => {
        editModal.style.display = 'none';
    });

    window.addEventListener('click', (e) => {
        if (e.target == editModal) {
            editModal.style.display = 'none';
        }
    });

    // --- INICIALIZAÇÃO ---
    carregarContratos();
});