<?php
$titulo_pagina = "Desafios Literários"; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'includes/conexao.php';

// Se o leitor não estiver logado, é expulso para a página de login
if (!isset($_SESSION['utilizador_id'])) {
    header("Location: autenticacao.php?erro=acesso_negado");
    exit();
}

$utilizador_id = $_SESSION['utilizador_id'];

if (isset($_GET['acao']) && $_GET['acao'] == 'resgatar') {
    $progresso_id = intval($_GET['id']);
    
    $sql_busca = "SELECT p.desafio_id, d.recompensa_pontos 
                  FROM progresso_desafios p 
                  JOIN desafios d ON p.desafio_id = d.id 
                  WHERE p.id = $progresso_id AND p.utilizador_id = $utilizador_id AND p.estado = 'concluido'";
    $res_busca = mysqli_query($conexao, $sql_busca);
    
    if (mysqli_num_rows($res_busca) > 0) {
        $dados_missao = mysqli_fetch_assoc($res_busca);
        $pontos_ganhos = $dados_missao['recompensa_pontos'];
        
        // A: Atualiza o estado da missão para 'resgatado'
        mysqli_query($conexao, "UPDATE progresso_desafios SET estado = 'resgatado' WHERE id = $progresso_id");
        
        // B: Soma os pontos e o XP na carteira real do utilizador no Workbench
        mysqli_query($conexao, "UPDATE utilizadores SET pontos = pontos + $pontos_ganhos, xp = xp + ($pontos_ganhos * 2) WHERE id = $utilizador_id");
        
        $sql_user = mysqli_query($conexao, "SELECT xp, nivel FROM utilizadores WHERE id = $utilizador_id");
        $user_data = mysqli_fetch_assoc($sql_user);
        if ($user_data['xp'] >= 1000) {
            mysqli_query($conexao, "UPDATE utilizadores SET nivel = nivel + 1, xp = xp - 1000 WHERE id = $utilizador_id");
        }
        
      
        $_SESSION['utilizador_pontos'] += $pontos_ganhos;
    }
    
    header("Location: desafios.php?sucesso=pontos");
    exit();
}

$sql_leitor = "SELECT * FROM utilizadores WHERE id = $utilizador_id";
$res_leitor = mysqli_query($conexao, $sql_leitor);
$leitor = mysqli_fetch_assoc($res_leitor);


$sql_missoes = "SELECT p.id AS progresso_id, p.desafio_id, p.estado, d.titulo, d.descricao, d.frequencia, d.recompensa_pontos 
                FROM progresso_desafios p
                JOIN desafios d ON p.desafio_id = d.id
                WHERE p.utilizador_id = $utilizador_id 
                ORDER BY d.frequencia ASC";
$resultado_missoes = mysqli_query($conexao, $sql_missoes);

include 'includes/header.php';
?>

<main class=" fundoB challenges-section">

    <div class="challenges-container">
        
        <!-- CABEÇALHO -->
        <header class="challenges-header">
            <h1>Missões e Desafios Ativos</h1>
            <p>Complete as suas tarefas literárias para acumular moedas virtuais e obter grandes descontos em livros físicos!</p>
        </header>

        <!-- PAINEL DE PROGRESSO -->
        <section class="user-progress-card">
            <div class="user-avatar-zone">
                <span class="avatar-emoji">🦉</span>
                <div>
                    <h2 class="text-white"><?php echo htmlspecialchars($leitor['nome']); ?>!</h2>
                    <p class="text-white">Nível Atual: <strong>Nível <?php echo $leitor['nivel']; ?></strong></p>
                </div>
            </div>
            
            <div class="progress-bar-zone">
                <div class="progress-text progress-text--light">
                    <span>Progresso do Nível</span>
                    <span><?php echo $leitor['xp']; ?> / 1000 XP</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width: <?php echo ($leitor['xp'] / 10); ?>%;"></div>
                </div>
            </div>

            <div class="wallet-zone" style="text-align:center;">
                <span class="wallet-label">Os Teus Pontos:</span>
                <span class="wallet-amount"><?php echo number_format($leitor['pontos'], 0, '', '.'); ?> 🪙</span>
            </div>
        </section>

        
        <!-- Mensagem de Sucesso ao ganhar pontos -->
        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 'pontos'): ?>
            <div class="info-box">
                🪙 Pontos adicionadas à sua carteira! Continue a ler para acumular mais!
            </div>
        <?php endif; ?>

        <!-- LISTA DE MISSÕES REAIS CARREGADAS -->
        <div class="challenges-list">
            
            <?php if (mysqli_num_rows($resultado_missoes) > 0): ?>
                <?php while ($missao = mysqli_fetch_assoc($resultado_missoes)): 
                    $emoji = "📖";
                    if ($missao['frequencia'] == 'weekly') $emoji = "✍️";
                    if ($missao['frequencia'] == 'special') $emoji = "🏆";
                ?>
                    <article class="challenge-card <?php echo $missao['frequencia']; ?>">
                        <div class="challenge-icon"><?php echo $emoji; ?></div>
                        
                        <div class="challenge-details challenge-details--grow">
                            <h3><?php echo htmlspecialchars($missao['titulo']); ?></h3>
                            <p><?php echo htmlspecialchars($missao['descricao']); ?></p>
                            
                            <div class="challenge-progress-row">
                                <div class="mini-progress-bar mini-progress-bar--light">
                                    <div class="mini-fill" style="width: <?php echo ($missao['estado'] == 'em_progresso') ? '35%' : '100%'; ?>;"></div>
                                </div>
                                <span class="progress-numeric progress-numeric--muted">
                                    <?php 
                                        if ($missao['estado'] == 'em_progresso') echo "Em andamento";
                                        if ($missao['estado'] == 'concluido') echo "Pronto a Resgatar!";
                                        if ($missao['estado'] == 'resgatado') echo "Missão Finalizada 🎉";
                                    ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="challenge-reward-zone" style="text-align: right; display: flex; flex-direction: column; gap: 10px; min-width: 110px;">
                            <span class="reward-points" style="font-size: 16px; font-weight: bold; color: #2ec4b6;">+<?php echo $missao['recompensa_pontos']; ?> pt</span>
                            
                        
                            <?php if ($missao['estado'] == 'em_progresso'): ?>
                                <?php 
                                    $link_destino = "livros-fisicos.php"; 
                                    $texto_botao = "Ir Ler";

                                    if ($missao['desafio_id'] == 2) {
                                        $link_destino = "livro.php"; 
                                        $texto_botao = "Fazer Resenha";
                                    } elseif ($missao['desafio_id'] == 3) {
                                        $link_destino = "livros-fisicos.php";
                                        $texto_botao = "Ir para a Loja";
                                    }
                                ?>
                                <button class="btn btn-secondary" onclick="window.location.href='<?php echo $link_destino; ?>'"><?php echo $texto_botao; ?></button>
                                
                            <?php elseif ($missao['estado'] == 'concluido'): ?>
                                <a href="desafios.php?acao=resgatar&id=<?php echo $missao['progresso_id']; ?>" class="btn btn-primary">Recolher 🪙</a>
                            <?php else: ?>
                                <button class="btn btn-secondary disabled" disabled>Recebido</button>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">Nenhuma missão atribuída ao seu perfil de momento.</p>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php 
include 'includes/footer.php'; 
?>
