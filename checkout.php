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

// O PHP processa a gravação APENAS quando o formulário é submetido via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['finalizar_transacao'])) {
    
    // 1. Captura e higieniza os dados enviados pelo formulário (Boa prática de segurança contra ataques)
    $nome_envio  = mysqli_real_escape_string($conexao, htmlspecialchars($_POST['nome_envio'], ENT_QUOTES, 'UTF-8'));
    $data_nasc   = mysqli_real_escape_string($conexao, htmlspecialchars($_POST['data_nascimento'], ENT_QUOTES, 'UTF-8'));
    $morada      = mysqli_real_escape_string($conexao, htmlspecialchars($_POST['morada'], ENT_QUOTES, 'UTF-8'));

    // 2. Calcula o valor total do carrinho atual do utilizador
    $sql_subtotal = mysqli_query($conexao, "SELECT SUM(preco * quantidade) AS total FROM carrinho WHERE utilizador_id = $utilizador_id");
    $row_subtotal = mysqli_fetch_assoc($sql_subtotal);
    $subtotal = floatval($row_subtotal['total']);

    // 3. Verifica se existem pontos de desconto aplicados na sessão
    $pontos_gastos = isset($_SESSION['pontos_desconto_aplicado']) ? intval($_SESSION['pontos_desconto_aplicado']) : 0;
    $valor_desconto = $pontos_gastos / 100;
    $total_final = max(0, $subtotal - $valor_desconto);

    // 4. Só avança se o carrinho não estiver vazio
    if ($subtotal > 0) {
        
        // Prepara a query de inserção (com todas as variáveis devidamente preenchidas agora)
        $sql_salvar = "INSERT INTO encomendas (utilizador_id, nome_envio, data_nascimento, morada, total, estado) 
                       VALUES ($utilizador_id, '$nome_envio', '$data_nasc', '$morada', $total_final, 'pago')";
        
        if (mysqli_query($conexao, $sql_salvar)) {
            
            // ==========================================
            // LOGICA DE SOMA DE PONTOS POR COMPRA 
            // ==========================================
            // Procurar os pontos atuais do utilizador para saber o nível e o multiplicador dele
            $sql_user_atual = mysqli_query($conexao, "SELECT pontos FROM utilizadores WHERE id = $utilizador_id");
            $row_user_atual = mysqli_fetch_assoc($sql_user_atual);
            $pontos_antes_da_compra = $row_user_atual ? intval($row_user_atual['pontos']) : 0;

            // Definir o multiplicador com base no XP atual dele
            $multiplicador = 1.0; // Iniciante (padrão)
            if ($pontos_antes_da_compra >= 2500) {
                $multiplicador = 1.5; // Mestre Literário ganha 50% mais pontos
            } elseif ($pontos_antes_da_compra >= 1000) {
                $multiplicador = 1.2; // Devorador ganha 20% mais pontos
            }

            // Calcular os pontos finais a adicionar (Base de 50 pontos * Multiplicador)
            $pontos_base_ganhos = 50;
            $pontos_finais_ganhos = round($pontos_base_ganhos * $multiplicador);

            // Atualiza o saldo do utilizador na Base de Dados
            if ($pontos_gastos > 0) {
                // Remove os pontos do desconto e adiciona os pontos novos da compra física
                mysqli_query($conexao, "UPDATE utilizadores SET pontos = pontos - $pontos_gastos + $pontos_finais_ganhos WHERE id = $utilizador_id");
            } else {
                // Se não usou desconto, apenas adiciona os novos pontos da compra
                mysqli_query($conexao, "UPDATE utilizadores SET pontos = pontos + $pontos_finais_ganhos WHERE id = $utilizador_id");
            }

            // Limpa o carrinho e finaliza a sessão de desconto
            mysqli_query($conexao, "DELETE FROM carrinho WHERE utilizador_id = $utilizador_id");
            unset($_SESSION['pontos_desconto_aplicado']);
            
            // Redireciona com feedback de sucesso
            header("Location: carrinho.php?sucesso=compra&pontos_ganhos=$pontos_finais_ganhos");
            exit();
        }
    }
} // Fecha o bloco processador do POST com segurança

include 'includes/header.php';
?>

<main class="catalog-section fundoC">
    <div class="single-book-container">

        <div class="support-form-box">
            <div class="admin-box-header">
                <h2>Dados de Envio Postal</h2>
                <p class="muted-small">Insira as informações obrigatórias para concluir a transação.</p>
            </div>

            <!-- Caixa de erro controlada dinamicamente pelo JavaScript -->
            <div id="js-error-msg" class="js-error-message"></div>

            <form id="formCheckout" action="checkout.php" method="POST" class="admin-form">
                <!-- Este input garante que o PHP sabe que a transação deve ser finalizada -->
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
