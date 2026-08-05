<?php
$titulo_pagina = "Seu Perfil"; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/conexao.php';

if (!isset($_SESSION['utilizador_id'])) {
    header("Location: autenticacao.php?erro=acesso_negado");
    exit();
}

$utilizador_id = $_SESSION['utilizador_id'];

//  CONSULTA: Dados do Leitor
$sql_leitor = "SELECT * FROM utilizadores WHERE id = $utilizador_id";
$res_leitor = mysqli_query($conexao, $sql_leitor);
$leitor = mysqli_fetch_assoc($res_leitor);

//  CONSULTA: Histórico de Encomendas
$sql_encomendas = "SELECT * FROM encomendas WHERE utilizador_id = $utilizador_id ORDER BY data_compra DESC";
$resultado_enc = mysqli_query($conexao, $sql_encomendas);

include 'includes/header.php';
?>

<main class="fundoC">
    <div class="catalog-container">

        <!-- CARTÃO DO PERFIL -->
        <section class="profile-card">
            <div class="profile-meta">
                <span class="avatar-circle">🦉</span>
                <div>
                    <h2 class="text-white">Olá <?php echo htmlspecialchars($leitor['nome']); ?>!</h2>
                    <p class="text-white">Membro ativo desde: <?php echo date('d/m/Y', strtotime($leitor['data_registo'])); ?></p>
                </div>
            </div>
            <div class="profile-balance">
                <span class="balance-label">O Teu Saldo</span>
                <strong class="balance-amount"><?php echo $leitor['pontos']; ?> Pontos</strong>
            </div>
        </section>

        <!-- SECÇÃO: HISTÓRICO DE ENCOMENDAS -->
        <section class="box">
            <div class="admin-box-header">
                <h3>As Minhas Encomendas</h3>
                <p class="muted-small">Acompanhe o estado de envio dos seus livros físicos comprados.</p>
            </div>

            <div class="orders-list">
                <?php if (mysqli_num_rows($resultado_enc) > 0): ?>
                    <?php while ($enc = mysqli_fetch_assoc($resultado_enc)): 
                        $classe_borda = ($enc['estado'] == 'pago') ? 'order-border-pago' : (($enc['estado'] == 'enviado') ? 'order-border-enviado' : 'order-border-outro');
                    ?>
                        <div class="order-item <?php echo $classe_borda; ?>">
                            <div>
                                <span class="order-id">Encomenda #<?php echo $enc['id']; ?></span>
                                <small class="order-date">Realizada em: <?php echo date('d/m/Y H:i', strtotime($enc['data_compra'])); ?></small>
                            </div>
                            <div>
                                <strong class="order-amount"><?php echo number_format($enc['total'], 2, ',', ''); ?> €</strong>
                                <span class="<?php echo ($enc['estado'] == 'pago') ? 'estado-pago' : (($enc['estado'] == 'enviado') ? 'estado-enviado' : 'estado-outro'); ?>"><?php echo $enc['estado']; ?></span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-orders">Ainda não efetuou nenhuma compra de livros físicos.</p>
                <?php endif; ?>
            </div>
        </section>

    </div>
</main>

<?php include 'includes/footer.php'; ?>
