<?php 

$titulo_pagina = "Autenticação"; 
include 'includes/header.php'; 

if (isset($_SESSION['utilizador_id'])) {
    header("Location: desafios.php");
    exit();
}
?>



<main class="single-book-section">
    <div class="single-book-container">

        <!-- EXIBIÇÃO DE ALERTAS DE ERRO EM PHP CRIAR CLASSE-->
        <?php if (isset($_GET['erro'])): ?>
            <div class="error-message">
                <?php 
                    if ($_GET['erro'] == 'dados_invalidos') echo "❌ E-mail ou senha incorretos. Por favor, tente novamente.";
                    if ($_GET['erro'] == 'email_existe') echo "⚠️ Este e-mail já está registado no BookHub! Tente fazer login.";
                    if ($_GET['erro'] == 'acesso_negado') echo "🔒 Precisa de entrar na sua conta primeiro para aceder a essa página.";
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['info']) && $_GET['info'] == 'sessao_encerrada'): ?>
            <div class="info-box">
                🚪 Sessão encerrada com segurança. Volte sempre!
            </div>
        <?php endif; ?>
        
      
        <div class="flex-wrap-center">

            <!-- COLUNA 1: FORMULÁRIO DE LOGIN (JÁ SOU LEITOR) -->
            <div class=" box support-form-box">
                <div class="admin-box-header">
                    <h2>Já sou Leitor 🦉</h2>
                    <p>Introduza as suas credenciais para entrar na conta.</p>
                </div>

                <form action="processar-login.php" method="POST" class="admin-form">
                    <div class="form-field">
                        <label for="login_email" class="label-form">E-mail:</label>
                        <input type="email" id="login_email" name="email" required placeholder="Ex: leitor@email.com" class="input-form">
                    </div>
                    
                    <div class="form-field">
                        <label for="login_senha" class="label-form">Senha:</label>
                        <input type="password" id="login_senha" name="senha" required placeholder="A sua senha" class="input-form">
                    </div>
                    
                    <button type="submit" class=" btn btn-primary">
                        🔓 Entrar na Conta
                    </button>
                </form>
            </div>

            <!-- COLUNA 2: FORMULÁRIO DE REGISTO (CRIAR NOVA CONTA) -->
            <div class=" box support-form-box">
                <div class="admin-box-header">
                    <h2>Criar Nova Conta ✨</h2>
                    <p>Registe-se para começar a ganhar pontos de leitura.</p>
                </div>

                <form action="processar-registo.php" method="POST" class="admin-form">
                    <div class="form-field">
                        <label for="reg_nome" class="label-form">Nome Completo:</label>
                        <input type="text" id="reg_nome" name="nome" required placeholder="Ex: João Silva" class="input-form">
                    </div>
                    
                    <div class="form-field">
                        <label for="reg_email" class="label-form">E-mail de Acesso:</label>
                        <input type="email" id="reg_email" name="email" required placeholder="Ex: joao@email.com" class="input-form">
                    </div>
                    
                   <div class="form-field">
                    <label for="reg_senha" class="label-form">Criar Senha:</label>
                    <input type="password" id="reg_senha" name="senha" required placeholder="Digite a sua senha segura" class="input-form">
                    
                    <!-- TEXTO DOS REQUISITOS EXIBIDO DIRETAMENTE NO SITE -->
                    <small class="form-note">
                        🔒 <strong>Requisitos de Segurança:</strong> Mínimo de 6 caracteres, contendo pelo menos 1 letra maiúscula, 1 letra minúscula, 1 número e 1 símbolo (@$!%*?&.).
                    </small>
                </div>

                    
                    
                    <button type="submit" class=" btn btn-primary">
                        🚀 Concluir Registo
                    </button>
                </form>
            </div>

        </div>
    </div>
</main>

<?php 
include 'includes/footer.php'; 
?>
