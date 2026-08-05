<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (isset($_SESSION['utilizador_id'])) {
    $link_perfil = "perfil.php"; 
} else {
    $link_perfil = "autenticacao.php";
}

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Bem-vindo ao BookHub, a maior comunidade literária gamificada. Avalie obras, participe em desafios e ganhe descontos exclusivos na nossa livraria." />
    <meta name="keywords" content="bookhub, livros, ler, comunidade literaria, gamificacao de leitura, resenhas literarias, criticas de livros, comprar livros com pontos, livraria online portugal, descontos em livros, desafios de leitura, xp de leitura'" />

    
    <!-- título dinâmico -->
    <title><?php echo isset($titulo_pagina) ? $titulo_pagina . " | BookHub" : "BookHub - Conecta as tuas leituras"; ?></title>

    <!-- Google Fonts -->
    <link href="https://googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@700;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">    

    <!-- Font Awesome para os Ícones -->
    <script src="https://kit.fontawesome.com/1bca288182.js" crossorigin="anonymous"></script>
    
    <!-- CSS-->
    <link rel="stylesheet" href="css/style.css">
    
</head>
<body>

<!-- O MENU DO BOOTSTRAP -->
<header class="main-header border-bottom">
    <nav class="navbar" aria-label="Navegação Principal do BookHub">
        <div class="container-fluid px-4">
            
            <div class="d-flex align-items-center gap-3">
                <!-- Botão Hambúrguer -->
                <button class="navbar-toggler border-0 p-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuLateralBookHub" aria-controls="menuLateralBookHub" aria-label="Abrir menu de navegação">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- logo-->
                <a href="index.php">
                <img src="imagens/logo-claro.png" width="150" alt="Logótipo BookHub com fundo claro" title="Logo BookHub com fundo claro">
                </a>
            </div>

            <!-- Barra de Pesquisa -->
            <div class="d-none d-md-flex mx-auto w-50 max-width-500">
                <form action="livros-fisicos.php" method="GET" style="width: 100%; display: flex; align-items: center;">
                    <div class="input-group">
                        <input type="text" name="pesquisa" class="form-control rounded-start-pill border-end-0 bg-light" placeholder="Pesquisar livros por título ou autor..." aria-label="Pesquisar livros" value="<?php echo isset($_GET['pesquisa']) ? htmlspecialchars($_GET['pesquisa']) : ''; ?>">
                        
                        <button type="submit" class="input-group-text rounded-end-pill bg-light border-start-0 text-muted" style="border: 1px solid #cbd5e1; cursor: pointer; border-left: none;">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </form>
            </div>


            <!-- Ícones Utilitários -->
            <div class="header-actions d-flex align-items-center gap-3">
                <a href="carrinho.php" class="action-icon text-dark fs-5 position-relative" aria-label="Ver carrinho de compras">
                    <i class="fa-solid fa-bag-shopping" style="color: #f2aa00;"></i>
                </a>
                
                <!-- Link do Login ou perfil-->
                <a href="<?php echo $link_perfil; ?>" class="profile-action text-dark d-flex align-items-center gap-2 text-decoration-none" aria-label="Ir para a minha conta">
                   <img src="imagens/coruja-piscando.png" width="80" alt="Mascote Coruja piscando" title="Mascote Coruja piscando">
                </a>
            </div>

        </div>
    </nav>
</header>

<!-- MENU LATERAL (OFFCANVAS) -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="menuLateralBookHub" aria-labelledby="menuLateralBookHubLabel" style="background-color: var(--cor-fundo); border-right: 1px solid var(--cor-secundaria-azul);">
    <div class="offcanvas-header border-bottom">
         <a href="index.php">
                    <img src="imagens/logo-claro.png" width="150" alt="Logótipo BookHub"  title="Logo BookHub com fundo claro">
                </a>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Fechar menu"></button>
    </div>
    <div class="offcanvas-body p-4">
        <ul class="nav flex-column gap-3 fs-5 fw-medium">
            <li class="nav-item"><a class="nav-link text-dark p-0" href="index.php"><i class="fa-thin fa-house me-2"></i> Início</a></li>
        
            <li class="nav-item"><a class="nav-link text-dark p-0" href="livros-fisicos.php"><i class="fa-thin fa-book-open me-2"></i> Vitrine de Livros</a></li>
            <li class="nav-item"><a class="nav-link text-dark p-0" href="desafios.php"><i class="fa-thin fa-trophy me-2"></i> Desafios Literários</a></li>
            <li class="nav-item"><a class="nav-link text-dark p-0" href="contactos.php"><i class="fa-thin fa-envelope me-2"></i> Contactos</a></li>
            
            <!-- Link da Administração: Para Admin logado -->
            <?php if (isset($_SESSION['utilizador_id']) && $_SESSION['utilizador_tipo'] === 'admin'): ?>
                <li class="nav-item border-top pt-3 mt-2"><a class="nav-link text-dark p-0 text-muted small" href="admin.php"><i class="fa-thin fa-lock me-2"></i> Área de Administração</a></li>
            <?php endif; ?>

            <!-- Botão de Sair condicionado-->
            <?php if (isset($_SESSION['utilizador_id'])): ?>
                <li class="nav-item border-top pt-3 mt-2">
                    <a class="nav-link p-0" href="logout.php" style="color: #ef4444; font-weight: bold; text-decoration: none;">
                        🚪 Terminar Sessão (Sair)
                    </a>
                </li>
                <li class="nav-item border-top pt-3 mt-2">
                    <a class="nav-link text-dark p-0" href="perfil.php" font-weight: bold; text-decoration: none;>
                        Perfil
                    </a>
                </li>
                 

            <?php endif; ?>

        </ul>
    </div>
</div>
