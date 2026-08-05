<?php 

$titulo_pagina = "Sua Resenha"; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/conexao.php';

$mensagem_resenha = "";

// Recolher resenhas
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao_resenha'])) {
    
    if (!isset($_SESSION['utilizador_id'])) {
        header("Location: autenticacao.php?erro=acesso_negado");
        exit();
    }
    
    $utilizador_id   = $_SESSION['utilizador_id'];
    $livro_google_id = 'local_book'; // Como é simplificado, fixamos um ID padrão
    $titulo_livro    = mysqli_real_escape_string($conexao, $_POST['titulo_livro']);
    $classificacao   = intval($_POST['classificacao']);
    $texto_resenha   = mysqli_real_escape_string($conexao, $_POST['texto_resenha']);
    
    // Insere os dados na tabela 'resenhas' com estado 'pendente'
    $sql_resenha = "INSERT INTO resenhas (utilizador_id, livro_google_id, titulo_livro, classificacao, texto_resenha, estado) 
                    VALUES ($utilizador_id, '$livro_google_id', '$titulo_livro', $classificacao, '$texto_resenha', 'pendente')";
                    
    if (mysqli_query($conexao, $sql_resenha)) {
        $mensagem_resenha = "
            <div style='background: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; font-size: 15px; margin-bottom: 25px; font-weight: bold; text-align: center;'>
                📝 Resenha enviada com sucesso! Assim que o administrador aprovar no painel, receberá +100 Pontos!
            </div>";
    } else {
        $mensagem_resenha = "
            <div style='background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; font-size: 15px; margin-bottom: 25px; font-weight: bold; text-align: center;'>
                ❌ Erro ao guardar no sistema: " . mysqli_error($conexao) . "
            </div>";
    }
}

include 'includes/header.php'; 
?>

<main class="single-book-section fundoC">
    <div class="single-book-container" style="max-width: 650px; margin: 0 auto; width: 100%;">
        
        <!-- Alerta de sucesso ou erro do envio -->
        <?php echo $mensagem_resenha; ?>

        <!-- ÁREA DE RESENHAS CRÍTICAS -->
        <section class="reviews-section">
            <div class="support-form-box">
                
                <div class="admin-box-header" style="border-bottom: 1px solid #eef2f5; padding-bottom: 15px; margin-bottom: 25px;">
                    <h2>Submeter Resenha Literária</h2>
                    <p>Escreva uma crítica sobre um livro comprado! Envie e ganhe <strong>+100 Pontos</strong>! ✍️</p>
                </div>
                
                <?php if (isset($_SESSION['utilizador_id'])): ?>
                    <form action="livro.php" method="POST" class="admin-form">
                        <input type="hidden" name="acao_resenha" value="1">

                        <div class="form-field" style="display: flex; flex-direction: column; margin-bottom: 15px;">
                            <label for="titulo_livro" class="label-form">Qual é o título do livro?</label>
                            <input type="text" id="titulo_livro" name="titulo_livro" required placeholder="Ex: Dune, O Hobbit..." class="input-form">
                        </div>

                        <div class="form-field" style="display: flex; flex-direction: column; margin-bottom: 15px;">
                            <label for="reviewRating" class="label-form">A sua classificação:</label>
                            <select id="reviewRating" name="classificacao" required class="input-form">
                                <option value="5">⭐⭐⭐⭐⭐ (Excelente)</option>
                                <option value="4">⭐⭐⭐⭐ (Muito Bom)</option>
                                <option value="3">⭐⭐⭐ (Razoável)</option>
                                <option value="2">⭐⭐ (Fraco)</option>
                                <option value="1">⭐ (Muito Mau)</option>
                            </select>
                        </div>
                        
                        <div class="form-field" style="display: flex; flex-direction: column; margin-bottom: 20px;">
                            <label for="reviewText" class="label-form">A sua Opinião / Resenha:</label>
                            <textarea id="reviewText" name="texto_resenha" rows="5" placeholder="Partilhe o que achou da história..." required minlength="20" class="input-form"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            🚀 Enviar Resenha para Avaliação
                        </button>
                    </form>
                <?php else: ?>
                    <div class="info-box">
                        🔒 Precisa de estar <a href="autenticacao.php" style="color: #ff9f1c; font-weight: bold; text-decoration: none;">ligado à sua conta</a> para submeter uma resenha.
                    </div>
                <?php endif; ?>

            </div>
        </section>
    </div>
</main>

<?php 
include 'includes/footer.php'; 
?>
