 <!-- includes/footer.php -->
<footer class="main-footer text-white">

    <!-- 1. Bloco Principal do Rodapé -->
    <div class="container ">
        <div class="row g-4 align-items-center">
            
            <!-- Coluna Esquerda-->
            <div class="col-12 col-md-7 text-center text-md-start">
               <img src="imagens/logo-escuro.png" alt="Logo BookHub com fundo escuro" title="Logo BookHub com fundo escuro" class="img-fluid" width="200em"  >
                <p class="mb-2 text-white-50">Conecta as tuas leituras, conquista os teus livros.</p>
                <p class="mb-2 text-white-50">O teu ponto central para ler e-books gratuitos e acumular pontos.</p>
                <p class="mb-0 text-white-50">Trabalho de Final de Curso - Avançado em Desenho e Programação e Web Sites.</p>
            </div>
            
            <!-- Coluna Direita -->
        
            <div class="col-12 col-md-5">
            <img src="imagens/fundo-escuro.png" alt="Mascote Coruja BookHub" title="Coruja Mascote BookHub no funfo escuro" class="img-fluid" style="max-height: 80px; filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.1));">

                <div class="p-4 rounded-4 shadow-sm text-dark border-0" style="background-color: rgba(250, 250, 250, 0.9);">
                    <h5 class="fw-bold mb-2" style="color: var(--cor-texto-escuro);"><i class="fa-solid fa-envelope-open-text me-2"></i> Junta-te ao Hub</h5>
                    <p class="small text-muted mb-3">Subscreve a nossa newsletter para receberes avisos de novos e-books e bónus de pontos semanais.</p>
                    
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="O teu melhor e-mail" aria-label="E-mail do utilizador">
                        <a href="mailto:suporte@bookhub.pt"><button class="btn btn-primary" type="button" > Aderir</button></a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
  
    <div class="sub-footer py-3 border-top border-secondary border-opacity-25" style="background-color: rgba(0, 0, 0, 0.15);">
        
        <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
            
            <div class="small text-white-50 text-center text-sm-start">
                &copy; <?php echo date('Y'); ?> <strong>BookHub</strong>. Todos os direitos reservados.
            </div>
            
            <div class="d-flex gap-3">
                <a href="https://www.instagram.com/" class="social-circle" aria-label="Seguir no Instagram">
                    <i class="fa-brands fa-instagram"  title=" Rede Social Intagram BookHub"></i>
                </a>
                <a href="https://www.tiktok.com/pt/" class="social-circle" aria-label="Seguir no TikTok">
                    <i class="fa-brands fa-tiktok"  title="Rede social tiktok BookHub"></i>
                </a>
                <a href="https://github.com/" class="social-circle" aria-label="Seguir no GitHub">
                    <i class="fa-brands fa-github"  title="Plataforma Github BookHub"></i>
                </a>
            </div>
            
        </div>
    </div>
    
</footer>

 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

</body>
</html>