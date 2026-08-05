<?php
$titulo_pagina = "Sua Sacola"; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/conexao.php';

if (!isset($_SESSION['utilizador_id'])) {
    header("Location: autenticacao.php?erro=acesso_negado");
    exit();
}

$utilizador_id = $_SESSION['utilizador_id'];
$mensagem_carrinho = "";

// 1. BUSCA O SALDO ATUAL DE PONTOS DO LEITOR
$sql_user = mysqli_query($conexao, "SELECT pontos FROM utilizadores WHERE id = $utilizador_id");
$user_data = mysqli_fetch_assoc($sql_user);
$pontos_disponiveis = $user_data['pontos'];

// AÇÕES DO CARRINHO

// AÇÃO: ADICIONAR ITEM
if (isset($_GET['acao']) && $_GET['acao'] == 'adicionar') {
    $livro_id = mysqli_real_escape_string($conexao, $_GET['id']);
    $titulo   = mysqli_real_escape_string($conexao, $_GET['titulo']);
    $preco    = floatval($_GET['preco']);
    $imagem   = mysqli_real_escape_string($conexao, $_GET['imagem']);

    $sql_check = "SELECT id, quantidade FROM carrinho WHERE utilizador_id = $utilizador_id AND livro_google_id = '$livro_id'";
    $res_check = mysqli_query($conexao, $sql_check);

    if (mysqli_num_rows($res_check) > 0) {
        $item_check = mysqli_fetch_assoc($res_check);
        mysqli_query($conexao, "UPDATE carrinho SET quantidade = quantidade + 1 WHERE id = " . $item_check['id']);
    } else {
        mysqli_query($conexao, "INSERT INTO carrinho (utilizador_id, livro_google_id, titulo_livro, preco, imagem_livro, quantidade) VALUES ($utilizador_id, '$livro_id', '$titulo', $preco, '$imagem', 1)");
    }
    header("Location: carrinho.php");
    exit();
}

// AÇÃO: REMOVER ITEM
if (isset($_GET['acao']) && $_GET['acao'] == 'remover') {
    $carrinho_id = intval($_GET['id']);
    mysqli_query($conexao, "DELETE FROM carrinho WHERE id = $carrinho_id AND utilizador_id = $utilizador_id");
    header("Location: carrinho.php");
    exit();
}

// AÇÃO: APLICAR DESCONTO DE PONTOS 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao_desconto'])) {
    $pontos_a_usar = intval($_POST['pontos_desconto']);
    
    if ($pontos_a_usar > $pontos_disponiveis) {
        $mensagem_carrinho = "<div style='background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 6px; font-weight: bold; margin-bottom: 20px;'>❌ Não tem pontos suficientes. O seu saldo é de $pontos_disponiveis pontos.</div>";
    } elseif ($pontos_a_usar < 100) {
        $mensagem_carrinho = "<div style='background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 6px; font-weight: bold; margin-bottom: 20px;'>⚠️ O limite mínimo para resgate é de 100 pontos (1 €).</div>";
    } else {
        // Guarda os pontos validados na sessão para abater no ecrã
        $_SESSION['pontos_desconto_aplicado'] = $pontos_a_usar;
        $mensagem_carrinho = "<div style='background: #d1fae5; color: #065f46; padding: 12px; border-radius: 6px; font-weight: bold; margin-bottom: 20px;'>✅ Desconto aplicado com sucesso!</div>";
    }
}

// AÇÃO: FINALIZAR COMPRA DEFINITIVA (Grava na BD abatendo os pontos gastos)
if (isset($_GET['acao']) && $_GET['acao'] == 'finalizar') {
    $sql_subtotal = mysqli_query($conexao, "SELECT SUM(preco * quantidade) AS total FROM carrinho WHERE utilizador_id = $utilizador_id");
    $row_subtotal = mysqli_fetch_assoc($sql_subtotal);
    $subtotal = floatval($row_subtotal['total']);

    $pontos_gastos = isset($_SESSION['pontos_desconto_aplicado']) ? $_SESSION['pontos_desconto_aplicado'] : 0;
    $valor_desconto = $pontos_gastos / 100; // Regra: 100 pontos = 1€
    
    $total_com_desconto = $subtotal - $valor_desconto;
    if ($total_com_desconto < 0) $total_com_desconto = 0; // Proteção para não dar negativo

    if ($subtotal > 0) {
        // Grava a encomenda final com o valor real calculado na Base de Dados
        mysqli_query($conexao, "INSERT INTO encomendas (utilizador_id, total, estado) VALUES ($utilizador_id, $total_com_desconto, 'pago')");
        
        // Deduz os pontos gastos da conta do utilizador no Workbench
        if ($pontos_gastos > 0) {
            mysqli_query($conexao, "UPDATE utilizadores SET pontos = pontos - $pontos_gastos WHERE id = $utilizador_id");
        }
        
        // Limpa o carrinho e limpa a sessão de desconto
        mysqli_query($conexao, "DELETE FROM carrinho WHERE utilizador_id = $utilizador_id");
        unset($_SESSION['pontos_desconto_aplicado']);
    }
    
    header("Location: carrinho.php?sucesso=compra");
    exit();
}

// BUSCA ITENS ATUAIS
$resultado_itens = mysqli_query($conexao, "SELECT * FROM carrinho WHERE utilizador_id = $utilizador_id ORDER BY data_adicionado DESC");
$subtotal_carrinho = 0;

include 'includes/header.php';
?>

<main class="catalog-section fundoC">
    <div class="catalog-container">
        
        <header class="catalog-header">
            <h1>O Seu Carrinho de Compras</h1>
            <p>Valide os seus livros e utilize o seu saldo de pontos para conseguir reduções de preço.</p>
        </header>

        <?php echo $mensagem_carrinho; ?>
        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 'compra'): ?>
            <div class="alert-success">
                🎉 Compra finalizada! A sua encomenda foi registada e os pontos foram atualizados no seu perfil!
            </div>
        <?php endif; ?>

        <div class="carrinho-wrapper">
            
            <?php if (mysqli_num_rows($resultado_itens) > 0): ?>
                
                <?php while ($item = mysqli_fetch_assoc($resultado_itens)): 
                    $subtotal_carrinho += ($item['preco'] * $item['quantidade']);
                ?>
                    <article class="book-card">
                        <div class="book-cover">
                            <img src="<?php echo $item['imagem_livro']; ?>" alt="Capa do livro" title=" Capa do livro">
                        </div>
                        <div class="book-info">
                            <h3 class="book-title"><?php echo $item['titulo_livro']; ?></h3>
                            <span class="book-cost"><?php echo number_format($item['preco'], 2, ',', ''); ?> €</span>
                            <small class="book-quantity">Quantidade: <?php echo $item['quantidade']; ?></small>
                        </div>
                        <div>
                            <a href="carrinho.php?acao=remover&id=<?php echo $item['id']; ?>" class="btn-remove" title="Remover item do carrinho no BookHub">❌ Remover</a>
                        </div>
                    </article>
                <?php endwhile; ?>

                <!-- SELETOR DE DESCONTO POR PONTOS -->
                <div class="support-form-box discount-box">
                    <h4>Sistema de Desconto Literário</h4>
                    <p class="muted-small">Tens <strong><?php echo $pontos_disponiveis; ?></strong> pontos. Cada 100 pontos equivalem a 1,00 € de desconto automático!</p>
                    
                    <form action="carrinho.php" method="POST" class="discount-form">
                        <input type="hidden" name="acao_desconto" value="1">
                        <input type="number" name="pontos_desconto" min="100" step="100" max="<?php echo $pontos_disponiveis; ?>" placeholder="Ex: 100, 200, 500..." class="input-small" required>
                        <button type="submit" class="btn-apply">Aplicar Redução</button>
                    </form>
                </div>

                               <!-- CÁLCULO FINAL -->
                <?php 
                    $pontos_aplicados = isset($_SESSION['pontos_desconto_aplicado']) ? $_SESSION['pontos_desconto_aplicado'] : 0;
                    $desconto_euros = $pontos_aplicados / 100;
                    $total_final = $subtotal_carrinho - $desconto_euros;
                    if ($total_final < 0) $total_final = 0;
                ?>
                <div class="summary-box">
                    <div class="summary-row">
                        <div>Subtotal:</div>
                        <div><?php echo number_format($subtotal_carrinho, 2, ',', ''); ?> €</div>
                    </div>
                    
                    <?php if ($pontos_aplicados > 0): ?>
                        <div class="summary-discount">
                            <span>Desconto Utilizado (<?php echo $pontos_aplicados; ?> pt):</span>
                            <span>- <?php echo number_format($desconto_euros, 2, ',', ''); ?> €</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="summary-total">
                        <h3>Total a Pagar:</h3>
                        <strong><?php echo number_format($total_final, 2, ',', ''); ?> €</strong>
                    </div>
                    
                    <div class="summary-actions">
                        <a href="livros-fisicos.php" class="btn btn-primary" title="Voltar à página da loja BookHub">📚 Continuar a comprar</a>
                        <a href="checkout.php" class="btn btn-primary" title="Continuar para finalizar compra BookHub">💳 Confirmar e Pagar</a>
                    </div>
                </div>

            <?php else: ?>
                <!-- CARRINHO VAZIO -->
                <div class="empty-cart-box">
                    <span class="empty-emoji">🛒</span>
                    <h3>O seu carrinho está vazio</h3>
                    <p>Acumule pontos lendo e troque por descontos em edições físicas.</p>
                    <a href="livros-fisicos.php" class="btn btn-primary"  title="Visitar vitrine de livros BookHub">Ver Livros Físicos</a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php 
include 'includes/footer.php'; 
?>
