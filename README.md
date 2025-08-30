# Gestor-Contratos
## Descrição
O Gestor-Contratos é uma aplicação web desenvolvida em PHP, projetada para gerenciar contratos e enviar alertas de vencimento automaticamente. A aplicação utiliza banco de dados para armazenar informações de contratos, entidades e soluções, permitindo uma gestão eficiente e organizada.

## Funcionalidades Principais
- **Cadastramento de Contratos**: Permite o cadastramento de novos contratos com informações como número do contrato, ID da entidade, ID da solução, data de início, data de vencimento e valor mensal.
- **Envio de Alertas**: Possui um script (`alertas.php`) que verifica contratos próximos do vencimento e envia alertas por e-mail para os destinatários configurados, utilizando a biblioteca PHPMailer.
- **Listagem de Contratos Ativos**: Exibe uma tabela com os contratos ativos, mostrando detalhes como número do contrato, entidade, solução, início, vencimento, valor mensal e anexo (PDF).

## Tecnologias Utilizadas
- **PHP**: Linguagem de programação utilizada para o desenvolvimento da aplicação.
- **PHPMailer**: Biblioteca PHP para envio de e-mails.
- ** HTML/CSS/JS**: Utilizados para a criação da interface web.

## Pré-requisitos
- Servidor web com suporte a PHP (recomendado Apache ou Nginx).
- Banco de dados (não especificado no código fornecido, mas presume-se necessário para armazenar informações de contratos).
- Composer para gerenciar dependências PHP.

## Como Instalar/Configurar
1. **Clonar o Repositório**: Clone o repositório Git para obter os arquivos da aplicação.
2. **Instalar Dependências**: Execute `composer install` para instalar as dependências PHP necessárias, como o PHPMailer.
3. **Configurar Banco de Dados**: Configure as conexões de banco de dados necessárias, editando os arquivos de configuração (`api/config.php`).
4. **Configurar Servidor Web**: Aponte o servidor web para a pasta do projeto.

## Como Usar
- **Acessar Interface Web**: Abra o navegador e acesse a URL onde a aplicação está hospedada.
- **Cadastrar Novo Contrato**: Preencha o formulário de cadastro de contrato com as informações necessárias e clique em "Salvar Contrato".
- **Visualizar Contratos Ativos**: Acesse a seção de listagem de contratos para visualizar os contratos ativos.

## Estrutura do Projeto
- **Root**:
  - `alertas.php`: Script para envio de alertas de vencimento.
  - `api/`: Pasta contendo arquivos de configuração e API.
  - `assets/`: Pasta com arquivos estáticos (CSS, JS, imagens).
  - `composer.json` e `composer.lock`: Arquivos de gerenciamento de dependências Composer.
  - `index.html`: Página principal da aplicação web.
  - `teste_email.php`: Script de teste para envio de e-mails.

## Contribuição
Contribuições são bem-vindas! Se você tem melhorias ou correções, por favor, faça um fork do projeto, realize as alterações e envie um pull request.

## Licença
Este projeto é licensiado sob [nome da licença]. Você é livre para modificar e distribuir o código, desde que mantenha a referência ao autor original e aos termos da licença.
