 <?php
$titulo_pagina = "Página de Administração"; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/conexao.php';

// Segurança: Bloqueia o acesso a utilizadores comuns
if (!isset($_SESSION['utilizador_id']) || $_SESSION['utilizador_tipo'] !== 'admin') {
    header("Location: autenticacao.php?erro=acesso_negado");
    exit();
}

$mensagem_alerta = "";

// Processamento dos formulários administrativos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao'])) {
    $acao = $_POST['acao'];

    if ($acao == 'cadastrar_desafio') {
        $titulo     = mysqli_real_escape_string($conexao, $_POST['nome']);
        $descricao  = mysqli_real_escape_string($conexao, $_POST['descricao']);
        $frequencia = mysqli_real_escape_string($conexao, $_POST['frequencia']);
        $recompensa = intval($_POST['recompensa']);

        $sql_desafio = "INSERT INTO desafios (titulo, descricao, frequencia, recompensa_pontos) 
                        VALUES ('$titulo', '$descricao', '$frequencia', $recompensa)";
        
        if (mysqli_query($conexao, $sql_desafio)) {
            $novo_desafio_id = mysqli_insert_id($conexao);
            $sql_users = "SELECT id FROM utilizadores WHERE tipo = 'leitor'";
            $resultado_users = mysqli_query($conexao, $sql_users);

            while ($user = mysqli_fetch_assoc($resultado_users)) {
                $user_id = $user['id'];
                mysqli_query($conexao, "INSERT INTO progresso_desafios (utilizador_id, desafio_id, estado) VALUES ($user_id, $novo_desafio_id, 'em_progresso')");
            }
            $mensagem_alerta = "<div class='sucesso-message'>🏆 Novo Desafio Literário ativado com sucesso! Todos os leitores já o receberam.</div>";
        }
    }

//Ação Atualizar stock 
if ($acao == 'atualizar_stock_preco') {
    $google_id  = mysqli_real_escape_string($conexao, $_POST['google_id']);
    $novo_preco = floatval($_POST['preco_unidade']);
    $nova_qtd   = intval($_POST['paginas']); 

    // Altera o preço e stock buscando pelo campo de texto 'google_id'
    $sql_update = "UPDATE livros SET preco = $novo_preco, paginas = $nova_qtd WHERE google_id = '$google_id'";
    
    if (mysqli_query($conexao, $sql_update)) {
        header("Location: admin.php?sucesso=atualizar_livro");
        exit();
    }
}

 // Ação  inserir produto
    if ($acao == 'inserir_produto') {
        $titulo = mysqli_real_escape_string($conexao, $_POST['titulo']);
        $autor  = mysqli_real_escape_string($conexao, $_POST['autor']);
        $preco  = floatval($_POST['preco']);
        $imagem = mysqli_real_escape_string($conexao, $_POST['imagem']);
        
        $google_id = 'local_' . uniqid(); 

        $sql_livro = "INSERT INTO livros (google_id, titulo, autor, preco, imagem) 
                      VALUES ('$google_id', '$titulo', '$autor', $preco, '$imagem')";
        
        if (mysqli_query($conexao, $sql_livro)) {
            header("Location: admin.php?sucesso=livro");
            exit();
        } else {
            $mensagem_alerta = "<div class='error-message'>❌ Erro ao salvar o livro: " . mysqli_error($conexao) . "</div>";
        }
    }
 

}
?>

<?php
// Monitorar encomendas

if (isset($_GET['acao_encomenda']) && isset($_GET['enc_id'])) {
    $enc_id = intval($_GET['enc_id']);
    $novo_estado = mysqli_real_escape_string($conexao, $_GET['acao_encomenda']);
    
    mysqli_query($conexao, "UPDATE encomendas SET estado = '$novo_estado' WHERE id = $enc_id");
    // Se o padrão PRG estiver ativo, redireciona, caso contrário preenche o alerta
    if (isset($mensagem_alerta)) {
        $mensagem_alerta = "<div class='sucesso-message'>📦 Estado da Encomenda #$enc_id atualizado para '$novo_estado'!</div>";
    } else {
        header("Location: admin.php?sucesso=encomenda");
        exit();
    }
}

// Aprovação de resenhas

if (isset($_GET['moderar']) && isset($_GET['id'])) {
    $resenha_id = intval($_GET['id']);
    $decisao = $_GET['moderar'];

    if ($decisao == 'aprovar') {
        $sql_res = mysqli_query($conexao, "SELECT utilizador_id FROM resenhas WHERE id = $resenha_id AND estado = 'pendente'");
        if (mysqli_num_rows($sql_res) > 0) {
            $res_data = mysqli_fetch_assoc($sql_res);
            $autor_resenha_id = $res_data['utilizador_id'];

            mysqli_query($conexao, "UPDATE resenhas SET estado = 'aprovada' WHERE id = $resenha_id");
            mysqli_query($conexao, "UPDATE utilizadores SET pontos = pontos + 100, xp = xp + 200 WHERE id = $autor_resenha_id");
            
            if (isset($mensagem_alerta)) {
                $mensagem_alerta = "<div class='sucesso-message'>✅ Resenha aprovada com sucesso! +100 Pontos creditados ao leitor.</div>";
            } else {
                header("Location: admin.php?sucesso=resenha_aprovada");
                exit();
            }
        }
    } elseif ($decisao == 'rejeitar') {
        mysqli_query($conexao, "UPDATE resenhas SET estado = 'rejeitada' WHERE id = $resenha_id");
        if (isset($mensagem_alerta)) {
            $mensagem_alerta = "<div class='error-message>❌ Resenha arquivada e rejeitada.</div>";
        } else {
            header("Location: admin.php?sucesso=resenha_rejeitada");
            exit();
        }
    }
}

// Consultas sql

$sql_encomendas = "SELECT e.*, u.nome AS nome_comprador FROM encomendas e JOIN utilizadores u ON e.utilizador_id = u.id ORDER BY e.data_compra DESC";
$resultado_encomendas = mysqli_query($conexao, $sql_encomendas);

$sql_pendentes = "SELECT r.*, u.nome AS nome_leitor FROM resenhas r JOIN utilizadores u ON r.utilizador_id = u.id WHERE r.estado = 'pendente' ORDER BY r.data_envio ASC";
$resultado_pendentes = mysqli_query($conexao, $sql_pendentes);

$sql_livros_lista = "SELECT * FROM livros ORDER BY criado_em DESC";
$resultado_livros_lista = mysqli_query($conexao, $sql_livros_lista);

include 'includes/header.php';
?>


<body>

    <main>
        <div class="admin-dashboard-layout">
            
            <aside class="admin-sidebar">
                <div class="admin-profile-summary">
                    <span class="admin-avatar">⚙️</span>
                    <div>
                        <h3 class="text-white">Admin Principal</h3>
                        <p class="text-white">Modo de Edição</p>
                    </div>
                </div>
                <nav class="admin-nav">
                    <a href="#encomendas" class="admin-nav-item " title="Gerir encomendas no BookHub admin">🛒 Encomendas</a>
                    <a href="#livros" class="admin-nav-item" title="Gerir livros no BookHub admin">📦 Gerir Catálogo</a>
                    <a href="#desafios" class="admin-nav-item" title="Gerir desafios no BookHub admin">🏆 Criar Desafios</a>
                    <a href="logout.php" class="admin-nav-item admin-logout" title="Logout BookHub admin">🚪 Encerrar Sessão</a>
                </nav>
            </aside>

    <div class="admin-content-area">
        
        <?php echo $mensagem_alerta; ?>

        <!-- Listagem de Encomendas Realizadas -->
        <section id="encomendas" class="admin-card-box">
            <div class="admin-box-header">
                <h2>Gestão de Encomendas de Livros Físicos</h2>
                <p>Consulte os pedidos pagos efetuados pelos utilizadores e faça a gestão do envio postal.</p>
            </div>

            <div>
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Leitor</th>
                            <th>Total</th>
                            <th>Data</th>
                            <th>Estado Atual</th>
                            <th>Ações de Envio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($resultado_encomendas) > 0): ?>
                            <?php while ($enc = mysqli_fetch_assoc($resultado_encomendas)): 
                                $classe_estado = ($enc['estado'] == 'pago') ? 'estado-pago' : (($enc['estado'] == 'enviado') ? 'estado-enviado' : 'estado-outro');
                            ?>
                                <tr>
                                    <td >#<?php echo $enc['id']; ?></td>
                                    <td ><?php echo htmlspecialchars($enc['nome_comprador']); ?></td>
                                    <td ><?php echo number_format($enc['total'], 2, ',', ''); ?> €</td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($enc['data_compra'])); ?></td>
                                    <td ><span class="<?php echo $classe_estado; ?>"><?php echo $enc['estado']; ?></span></td>
                                    <td >
                                        <?php if ($enc['estado'] == 'pago'): ?>
                                            <a href="admin.php?acao_encomenda=enviado&enc_id=<?php echo $enc['id']; ?>" class="btn btn-admin"  title="Processar encomendas BookHub admin">🚚 Marcar Enviado</a>
                                        <?php else: ?>
                                            <span>Processado</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" >Nenhuma encomenda pendente.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- SECÇÃO: CRIAR DESAFIOS -->
        <section id="desafios" class="admin-card-box">
            <div class="admin-box-header">
                <h2>Lançar Novo Desafio Literário</h2>
                <p>Crie novas missões para movimentar o sistema de pontos dos leitores reais.</p>
            </div>
            
            <form action="admin.php" method="POST" class="admin-form">
                <input type="hidden" name="acao" value="cadastrar_desafio">
                <div class="support-form-box" >
                        <label for="nome_desafio">Nome da Missão</label>
                        <input type="text" id="nome_desafio" name="nome" placeholder="Ex: Devorador de Páginas" required>

                        <label for="frequencia">Frequência</label>
                        <select id="frequencia" name="frequencia" required>
                            <option value="daily">Missão Diária</option>
                            <option value="weekly">Missão Semanal</option>
                            <option value="special">Missão Especial / Conquista</option>
                        </select>
                
                    <label for="descricao_desafio">Instruções para o Leitor</label>
                    <textarea id="descricao_desafio" claas="label-form" name="descricao" rows="3" placeholder="Descreva o que o utilizador precisa de fazer..." required></textarea>
                
                    <label for="recompensa_pontos">Valor do Prémio (Pontos)</label>
                    <input type="number" id="recompensa_pontos" name="recompensa" placeholder="Ex: 50" required>
                </div>
                <button type="submit" class="btn btn-admin" cursor:pointer;>🏆 Ativar Novo Desafio</button>
            </form>
          
        </section>
        

        <!-- Listagem e aprovação de Resenhas Pendentes -->
    <br>
        <section class="admin-card-box">
            <div class="admin-box-header">
                <h2>Moderação de Resenhas Literárias</h2>
                <p>Analise os comentários enviados pelos utilizadores. Aprove textos válidos para libertar os <strong>+100 Pontos</strong>.</p>
            </div>
           <div class="support-form-box" >
                <?php if (mysqli_num_rows($resultado_pendentes) > 0): ?>
                    <?php while ($resenha = mysqli_fetch_assoc($resultado_pendentes)): ?>
                        <div class="review-item-card">
                            <div class="support-form-wrapper">
                                    Enviado por: <strong><?php echo htmlspecialchars($resenha['nome_leitor']); ?></strong> | Livro: <strong><?php echo htmlspecialchars($resenha['titulo_livro']); ?></strong>
                
                                    <?php echo str_repeat("⭐", $resenha['classificacao']); ?>
                                <p>"<?php echo htmlspecialchars($resenha['texto_resenha']); ?>"
                                </p>
                             <div>
                                <a href="admin.php?moderar=aprovar&id=<?php echo $resenha['id']; ?>" class="btn btn-admin" title="Aprovar resenhas no BookHub admin">✔️ Aprovar (+100pt)</a>
                                <a href="admin.php?moderar=rejeitar&id=<?php echo $resenha['id']; ?>" class="btn btn-admin" title="Rejeitar resenhas no BookHub admin">❌ Rejeitar</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>📭 Nenhuma resenha pendente de aprovação neste momento.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- SECÇÃO: LISTA DE PRODUTOS E ATUALIZAÇÃO DE STOCK -->

     <!-- FORMULÁRIO INSERIR PRODUTO -->
        <section class="admin-card-box">
            <div class="admin-box-header">
                <h2>Inserir Novo Produto na Loja</h2>
                <p>Adicione um novo livro físico definindo o preço por unidade e a quantidade inicial de stock.</p>
            </div>
            <form action="admin.php" method="POST" class="admin-form" id="livros">

                    <div class="support-form-box">
                        <label for="titulo">Título do Livro</label>
                        <input type="text" id="titulo" name="titulo" placeholder="Ex: O Senhor dos Anéis" required>
            
                        <label for="autor">Autor da Obra</label>
                        <input type="text" id="autor" name="autor" placeholder="Ex: J.R.R. Tolkien" required >
                 
                        <label for="preco">Preço por Unidade (€)</label>
                        <input type="number" id="preco" name="preco" step="0.01" placeholder="Ex: 19.99" required >
               
                        <label for="quantidade">Quantidade Disponível (Stock Inicial)</label>
                        <input type="number" id="quantidade" name="quantidade" placeholder="Ex: 15" required >
                  
                        <label for="imagem">URL / Caminho da Capa</label>
                        <input type="text" id="imagem" name="imagem" value="https://placeholders.dev" required >
                        
                    </div>
                <button type="submit" class="btn btn-admin" cursor:pointer;">🚀 Inserir Produto na Base de Dados</button>
            </form>
        </section>
        
 <!-- SECÇÃO: LISTA DE PRODUTOS E CONTROLO DE STOCK -->
<section id="livros_lista" class="admin-card-box">
    <div class="admin-box-header">
        <h2>Lista de Produtos Existentes e Controlo de Stock</h2>
        <p>Consulte e atualize a quantidade disponível (stock) e o preço por unidade exigidos pelo enunciado.</p>
    </div>
    <div>
        <table class="tabela">
            <thead>
                <tr>
                    <th >Capa</th>
                    <th >Livro / Autor</th>
                    <th >Preço (€) e Stock Atual</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($resultado_livros_lista) > 0): ?>
                    <?php while ($livro = mysqli_fetch_assoc($resultado_livros_lista)): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($livro['imagem']); ?>" width="50" alt="Capa do livro" title="Capa do livro">
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($livro['titulo']); ?></strong><br>
                                <small >Por: <?php echo htmlspecialchars($livro['autor']); ?></small>
                            </td>
                            
         <!-- Formulário para Atualização de Preço e Quantidade (Stock) -->
                <td>
                    <form action="admin.php" method="POST">
                        <input type="hidden" name="acao" value="atualizar_stock_preco">
                        
                        <input type="hidden" name="google_id" value="<?php echo $livro['google_id']; ?>">
                        
                        <input type="number" name="preco_unidade" step="0.01" value="<?php echo $livro['preco']; ?>" class="support-form-box" required placeholder="Preço">
                        <input type="number" name="paginas" value="<?php echo $livro['paginas']; ?>" class="support-form-box" required placeholder="Stock">
                        
                        <button type="submit" class="btn btn-admin">
                            🔄 Atualizar
                        </button>
                    </form>
                </td>

                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3">Nenhum produto registado na base de dados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<!-- BOTÃO FIXO VOLTAR AO TOPO-->
<button type="button" id="btnTopo" class="btn btn-top position-fixed text-white d-flex bottom-0 end-0 m-4 shadow-lg align-items-center justify-content-center" aria-label="Voltar ao topo da página">
  TOP
</button>

<script>
const botaoTopo = document.getElementById("btnTopo");

window.onscroll = function() {
    if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
        botaoTopo.style.display = "block";
    } else {
        botaoTopo.style.display = "none";
    }
};

botaoTopo.addEventListener("click", () => {
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
});
botaoTopo.onmouseenter = function() { this.style.transform = "scale(1.1)"; };
botaoTopo.onmouseleave = function() { this.style.transform = "scale(1)"; };
</script>

</main>

<footer class="main-footer text-white" >
   
    <div class="container ">
        <div class="row g-4 align-items-center">
            
            <!-- Coluna Esquerda-->
            <div class="col-12 col-md-7 text-center text-md-start">
               <img src="imagens/logo-escuro.png" alt="Logo BookHub com fundo escuro" class="img-fluid" width="200em" alt="Logo BookHub com fundo escuro" title="Logo BookHub com fundo escuro">
                
                <p class="mb-0 text-white-50">Trabalho de Final de Curso - Avançado em Desenho e Programação e Web Sites</p>
            </div>
            
            <!-- Coluna Direita -->
        
            <div class="col-12 col-md-5">
            <img src="imagens/fundo-escuro.png" alt="Mascote Coruja BookHub" class="img-fluid mascot-img " alt="Mascote (coruja) BookHub no fundo escuro" title="Mascote (coruja) BookHub no fundo escuro">
            <p class="mb-2 text-white-50">O teu ponto central para administrares BookHub</p>
        </div>
    </div>
  
    <div class="sub-footer py-3 border-top border-secondary border-opacity-25">
        
        <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
            
            <div class="small text-white-50 text-center text-sm-start">
                &copy; <?php echo date('Y'); ?> <strong>BookHub</strong>. Todos os direitos reservados.
            </div>
    </div>
  

</footer>

 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

</body>
</html>
