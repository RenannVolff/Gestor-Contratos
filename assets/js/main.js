// /assets/js/main.js
document.addEventListener('DOMContentLoaded', function() {
    const apiBaseUrl = 'api/contratos/';
    const tabelaContratosBody = document.querySelector('#tabela-contratos tbody');
    const formNovoContrato = document.getElementById('form-novo-contrato');
    const mensagemDiv = document.getElementById('mensagem');

    async function carregarContratos() {
        try {
            const response = await fetch(`${apiBaseUrl}listar.php`);
            if (!response.ok) {
                throw new Error('Erro na rede: ' . response.statusText);
            }
            const contratos = await response.json();
            
            tabelaContratosBody.innerHTML = '';

            contratos.forEach(contrato => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${contrato.numero_contrato}</td>
                    <td>${contrato.entidade_nome}</td>
                    <td>${contrato.solucao_nome}</td>
                    <td>${new Date(contrato.data_inicio).toLocaleDateString('pt-BR')}</td>
                    <td>${new Date(contrato.data_vencimento).toLocaleDateString('pt-BR')}</td>
                    <td>R$ ${parseFloat(contrato.valor_mensal).toFixed(2)}</td>
                    <td>
                        ${contrato.caminho_anexo_pdf 
                            ? `<a href="${contrato.caminho_anexo_pdf}" target="_blank">Ver PDF</a>` 
                            : 'Nenhum'}
                    </td>
                `;
                tabelaContratosBody.appendChild(tr);
            });

        } catch (error) {
            tabelaContratosBody.innerHTML = `<tr><td colspan="7">Erro ao carregar contratos: ${error.message}</td></tr>`;
        }
    }

    formNovoContrato.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        mensagemDiv.textContent = 'Enviando...';

        try {
            const response = await fetch(`${apiBaseUrl}criar.php`, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.status === 'success') {
                mensagemDiv.textContent = result.message;
                mensagemDiv.style.color = 'green';
                this.reset();
                carregarContratos();
            } else {
                throw new Error(result.message || 'Ocorreu um erro.');
            }

        } catch (error) {
            mensagemDiv.textContent = `Erro: ${error.message}`;
            mensagemDiv.style.color = 'red';
        }
    });

    carregarContratos();
});