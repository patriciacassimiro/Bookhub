<?php
$titulo_pagina = "Checkout"; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/conexao.php';

if (!isset($_SESSION['utilizador_id'])) {
    header("Location: autenticacao.php?erro=acesso_negado");
    exit();
}

$utilizador_id = $_SESSION['utilizador_id'];

// O PHP agora apenas grava os dados se passarem pelo filtro do JavaScript
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['finalizar_transacao'])) {
    $nome_envio  = mysqli_real_escape_string($conexao, htmlspecialchars($_POST['nome_envio'], ENT_QUOTES, 'UTF-8'));
    $data_nasc   = mysqli_real_escape_string($conexao, htmlspecialchars($_POST['data_nascimento'], ENT_QUOTES, 'UTF-8'));
    $morada      = mysqli_real_escape_string($conexao, htmlspecialchars($_POST['morada'], ENT_QUOTES, 'UTF-8'));

    $sql_subtotal = mysqli_query($conexao, "SELECT SUM(preco * quantidade) AS total FROM carrinho WHERE utilizador_id = $utilizador_id");
    $row_subtotal = mysqli_fetch_assoc($sql_subtotal);
    $subtotal = floatval($row_subtotal['total']);

    $pontos_gastos = isset($_SESSION['pontos_desconto_aplicado']) ? $_SESSION['pontos_desconto_aplicado'] : 0;
    $valor_desconto = $pontos_gastos / 100;
    $total_final = max(0, $subtotal - $valor_desconto);

    if ($subtotal > 0) {
        $sql_salvar = "INSERT INTO encomendas (utilizador_id, nome_envio, data_nascimento, morada, total, estado) 
                       VALUES ($utilizador_id, '$nome_envio', '$data_nasc', '$morada', $total_final, 'pago')";
        
        if (mysqli_query($conexao, $sql_salvar)) {
            if ($pontos_gastos > 0) {
                mysqli_query($conexao, "UPDATE utilizadores SET pontos = pontos - $pontos_gastos WHERE id = $utilizador_id");
            }
            mysqli_query($conexao, "DELETE FROM carrinho WHERE utilizador_id = $utilizador_id");
            unset($_SESSION['pontos_desconto_aplicado']);
            
            header("Location: carrinho.php?sucesso=compra");
            exit();
        }
    }
}

include 'includes/header.php';
?>

<main class="catalog-section fundoC" >
    <div class="single-book-container" >

        <div class="support-form-box">
                <div class="admin-box-header" >
                <h2>Dados de Envio Postal</h2>
                <p class="muted-small">Insira as informações obrigatórias para concluir a transação.</p>
            </div>

            <!-- Caixa de erro controlada dinamicamente pelo JavaScript -->
            <div id="js-error-msg" class="js-error-message"></div>

            <form id="formCheckout" action="checkout.php" method="POST" class="admin-form">
                <input type="hidden" name="finalizar_transacao" value="1">

                <div class="form-field">
                    <label class="label-form">Nome Completo do Destinatário:</label>
                    <input type="text" id="nome_envio" name="nome_envio" required placeholder="Ex: Maria Ramos" class="input-form">
                </div>

                <div class="form-field">
                    <label class="label-form">Data de Nascimento:</label>
                    <input type="date" id="data_nascimento" name="data_nascimento" required class="input-form">
                </div>

                <div class="form-field">
                    <label class="label-form">Morada Completa de Envio:</label>
                    <textarea id="morada" name="morada" rows="3" required placeholder="Rua, número, andar e código postal..." class="input-form"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    📦 Concluir e Solicitar Envio
                </button>
            </form>
        </div>

    </div>
</main>

<script src="js/validacao-checkout.js"></script>

<?php include 'includes/footer.php'; ?>
