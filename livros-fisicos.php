<?php
$titulo_pagina = "Vitrine de Livros"; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/conexao.php';

// Barra de pesquisa integrada ao workbench
$termo_pesquisa = "";
$sql_query = "SELECT * FROM livros WHERE preco > 0 ORDER BY criado_em DESC LIMIT 24";

if (isset($_GET['pesquisa']) && !empty($_GET['pesquisa'])) {
    $termo_pesquisa = mysqli_real_escape_string($conexao, $_GET['pesquisa']);
    
    // Filtra no banco por títulos ou autores que contenham o que foi digitado
    $sql_query = "SELECT * FROM livros 
                  WHERE titulo LIKE '%$termo_pesquisa%' 
                  OR autor LIKE '%$termo_pesquisa%' 
                  ORDER BY criado_em DESC";
}

$sql = mysqli_query($conexao, $sql_query);

include 'includes/header.php'; 
?>


<main class="fundo">
    <h1>Selecione as suas obras favoritas e utilize os seus pontos para obter descontos.</h1>
    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">

        <?php while($livro = mysqli_fetch_assoc($sql)){ ?>
            
            <div class="col">
            
                <div class="card h-100 shadow-sm border-0 rounded-3 bg-white">
                    
                   
                    <div class="d-flex align-items-center justify-content-center bg-light rounded-top-3 book-image-wrapper book-image-wrapper-tall">
                        <img src="<?= htmlspecialchars($livro['imagem']) ?>"
                             alt="<?= htmlspecialchars($livro['titulo']) ?>"
                             class="img-fluid book-image">
                    </div>

                    <div class="card-body d-flex flex-column p-3">
                        <span class="text-warning fw-bold small text-uppercase mb-1">Livro Físico</span>
                        
                        <h5 class="card-title text-dark fw-bold mb-1 text-truncate" title="<?= htmlspecialchars($livro['titulo']) ?>">
                            <?= htmlspecialchars($livro['titulo']) ?>
                        </h5>
                        
                        <p class="card-text text-muted small mb-3">Por: <?= htmlspecialchars($livro['autor']) ?></p>

                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                            <span class="fs-5 fw-bold text-dark">
                                <?= number_format($livro['preco'],2,",",".") ?> €
                            </span>

                            <a href="carrinho.php?acao=adicionar&id=<?= urlencode($livro['google_id']) ?>&titulo=<?= urlencode($livro['titulo']) ?>&preco=<?= $livro['preco'] ?>&imagem=<?= urlencode($livro['imagem']) ?>"
                               class="btn btn-primary text-white fw-bold px-3 btn-sm">
                                 Adicionar
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        <?php }  ?>

    </div> 
</main> 
<?php

include 'includes/footer.php'; 
?>
