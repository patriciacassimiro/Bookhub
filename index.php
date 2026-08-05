<?php
include 'includes/header.php'; 

include 'includes/conexao.php';

    $sql = mysqli_query($conexao,"
    SELECT *
    FROM livros
    WHERE preco > 0
    ORDER BY criado_em DESC
    LIMIT 12
    ");
    ?>
<main>
    
    <section>
        <div class="position-relative p-4 p-md-5 overflow-hidden shadow-sm text-white hero-banner">

            <div class="position-absolute top-0 start-0 w-100 h-100 hero-overlay"></div>

            <div class="row align-items-center position-relative h-100 hero-content">

                <div class="col-12 col-md-8 text-center text-md-start mb-4 mb-md-0">
                    <span class="badge mb-3 px-3 py-2 text-dark fw-semibold hero-badge">
                        <i class="fa-solid fa-sparkles me-1"></i> Comunidade BookHub
                    </span>
                    <h1 class="display-5 fw-bold mb-3 text-white">
                        Olá, <span class="hero-highlight">Huber</span>! Seja bem vindo à Comunidade BookHub 👋
                    </h1>
                    <p class="lead mb-4 hero-lead">
                       O seu próximo capítulo começa aqui! Avalia os teus livros favoritos, acumula pontos com as tuas resenhas e ganhe descontos em livros da nossa loja!
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-md-start">
                        <a href="livros-fisicos.php" class="btn btn-primary"><i class="fa-solid fa-book-open me-2"></i> Visitar Vitrine de livros</a>
                        <a href="desafios.php" class="btn btn-outline-light d-inline-flex align-items-center justify-content-center"><i class="fa-solid fa-trophy me-2"></i> Ver Missões</a>
                    </div>
                </div>

                <div class="col-12 col-md-4 text-center">
                    <div class="p-4 rounded-4 border border-secondary border-opacity-25 d-inline-block text-white hero-level-box">
                        <i class="fa-solid fa-owl display-1 mb-2 hero-owl"></i>
                        <div class="mt-2">
                            <p class="fw-bold mb-1 level-label text-white">Nível Atual: <span class="text-uppercase text-warning">Iniciante</span></p>
                            <!-- Barra de progresso -->
                            <div class="progress bg-dark bg-opacity-50 level-progress">
                                <div class="progress-bar" role="progressbar" style="width: 10%;" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="d-block mt-2 level-small">Faltam 350 pts para subir!</small>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

  
   

      <!-- SECÇÃO DO CARROSSEL HORIZONTAL -->
    <section class="container my-5">
        <h2 class="fw-bold text-dark mb-4 section-title">Livros em Destaque</h2>
        
        <div class="d-flex flex-row flex-nowrap overflow-x-auto g-4 pb-3" id="carrosselLivrosPagos">
            
            <?php while($livro = mysqli_fetch_assoc($sql)){ ?>
                
                <div class="me-4 carousel-item-fixed">
                    
                    <div class="card h-100 shadow-sm border-0 rounded-3 bg-white">
                        
                        <div class="d-flex align-items-center justify-content-center bg-light rounded-top-3 book-image-wrapper">
                            <img src="<?= htmlspecialchars($livro['imagem']) ?>"
                                 alt="<?= htmlspecialchars($livro['titulo']) ?>"
                                 class="img-fluid book-image">
                        </div>

                        <div class="card-body d-flex flex-column p-3">
                            <span class="text-warning fw-bold small text-uppercase mb-1">Livro Físico</span>
                            
                            <h5 class="card-title text-dark fw-bold mb-1 text-truncate card-title-small" title="<?= htmlspecialchars($livro['titulo']) ?>">
                                <?= htmlspecialchars($livro['titulo']) ?>
                            </h5>
                            
                            <p class="card-text text-muted small mb-3 text-truncate">Por: <?= htmlspecialchars($livro['autor']) ?></p>

                            <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                <span class="fw-bold text-dark price-16">
                                    <?= number_format($livro['preco'],2,",",".") ?> €
                                </span>

                                <a href="carrinho.php?acao=adicionar&id=<?= urlencode($livro['google_id']) ?>&titulo=<?= urlencode($livro['titulo']) ?>&preco=<?= $livro['preco'] ?>&imagem=<?= urlencode($livro['imagem']) ?>"
                                   class="btn btn-primary"
                                  >
                                    🛒 Adicionar
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

            <?php } ?>

        </div>
    </section>

</main>

<?php
include 'includes/footer.php'; 
?>
