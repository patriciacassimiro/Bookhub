# 📚 BookHub — Plataforma Literária Gamificada

O **BookHub** é uma plataforma web responsiva orientada ao conceito *mobile-first* desenvolvida como Projeto Final de Curso. O ecossistema combina a disponibilização de e-books gratuitos e a venda de livros físicos com mecânicas de gamificação. Os utilizadores (apelidados de *Hubers*) ganham pontos e sobem de nível (XP) ao submeterem resenhas críticas após a leitura, desbloqueando multiplicadores de pontos e descontos comerciais exclusivos na loja física.

## 🚀 Começando

Estas instruções permitirão que obtenha uma cópia do projeto em operação na sua máquina local (localhost) para fins de desenvolvimento, avaliação e testes.

### 📋 Pré-requisitos

Para executar este projeto, necessita de ter instalado o seguinte ecossistema de software:

* **XAMPP** (Utilizado exclusivamente para correr o servidor web )
* **MySQL Workbench** 
* **Navegador Web** 
* **Visual Studio Code** 

### 🔧 Instalação

Siga o passo a passo para configurar o ambiente de desenvolvimento local:

1. **Clonar ou copiar o projeto:**
   Transfira os ficheiros do repositório e cole-os na pasta raiz do servidor Apache do XAMPP:
   ```bash
   C:\xampp\htdocs\bookhub\
   ```

2. **Configurar a Base de Dados no MySQL Workbench:**
   * Abra o **MySQL Workbench** e ligue-se à sua instância local do MySQL.
   * Crie um novo Schema (Base de Dados) chamado `bookhub_db`.
   * Execute o script SQL de modelação do projeto para criar a estrutura das tabelas (`utilizadores`, `produtos`, `encomendas`, `carrinho`).

3. **Inicializar o Servidor Apache:**
   * Abra o painel de controlo do XAMPP e inicialize **apenas** o módulo **Apache** (o serviço do banco de dados MySQL será gerido nativamente ou através do Workbench).

4. **Verificar os ficheiros de configuração:**
   Garanta que o ficheiro `includes/conexao.php` possui as credenciais de acesso corretas configuradas na sua instância do Workbench:
   ```php
   \$host = "localhost";
   \$user = "root";       // Ou o seu utilizador personalizado do Workbench
   \$pass = "";           // A sua palavra-passe definida no Workbench
   \$dbname = "bookhub_db";
   ```

5. **Executar a Plataforma:**
   Abra o seu navegador de internet e aceda ao endereço:
   
http://localhost/bookhub/index.php

## ⚙️ Executando os testes

Os testes estruturais do sistema garantem que as regras de negócio críticas definidas no briefing do projeto funcionam perfeitamente.

### 🔩 Analisar os testes de ponta a ponta

* **Teste de Maioridade (+18) no Checkout:** O ficheiro `js/validacao-checkout.js` barra tentativas de submissão de encomendas caso a data de nascimento inserida corresponda a um menor de 18 anos.
* **Teste de Atribuição de Pontos por Compra:** Ao concluir uma compra simulada no formulário de `checkout.php`, o sistema calcula o multiplicador correspondente ao nível de XP do utilizador, atualiza o saldo na tabela `utilizadores` e limpa os itens do carrinho na base de dados.
* **Teste de Interface Dinâmica (Mascote Coruja):** Ao alterar manualmente a pontuação de um utilizador no MySQL Workbench para `1200`, a página inicial (`index.php`) atualiza automaticamente o badge para "Devorador de Livros" e altera visualmente a expressão da mascote coruja.

### ⌨️ Testes de integração de API e Estilo de Código

* **Google Books API (`js/api.js`):** Validação do tempo de resposta (`Fetch`) e conversão automática de links de imagens de `http://` para `https://` para evitar bloqueios de conteúdo misto (*Mixed Content*) no navegador.
* **Segurança de Dados:** Verificação de sanitização do formulário contra ataques via `mysqli_real_escape_string` e `htmlspecialchars`.

## 🛠️ Construído com

As tecnologias e ferramentas utilizadas para desenvolver este ecossistema web foram:

* **PHP 8** - Linguagem estrutural para lógica de backend e queries dinâmicas.
* **JavaScript (ES6)** - Manipulação assíncrona para consumo da API e animações.
* **Bootstrap 5** - Framework CSS utilizado para estruturar a grelha responsiva (*mobile-first*).
* **Font Awesome** - Biblioteca de iconografia.
* **Google Books API** - API pública utilizada para alimentar o catálogo de e-books.
* **MySQL Workbench** - Ferramenta de modelação e administração de sistemas de bases de dados relacionais.
* **XAMPP** - Servidor web Apache local para testes.
* **Figma** - Software utilizado para a elaboração do *Style Guide* e dos *wireframes* do protótipo.


## 🎁 Agradecimentos

* Agradecimento público aos orientadores e avaliadores do curso pelo acompanhamento técnico 🫂;
* Um agradecimento especial à comunidade de código aberto pelas ferramentas disponibilizadas 🚀;
* Obrigada por teres chegado até aqui! 🦉.

---
Com dedicação,
Patricia Cassimiro.
