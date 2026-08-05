<?php 
$titulo_pagina = "Contactos e Suporte"; 

include 'includes/header.php'; 
?>

<main>
      
<section class="support-section">
    <div class="support-container">
        
        <!-- COLUNA DA ESQUERDA: Informações de Contacto -->
        <div class="support-info">
            <span class="badge-status">Estamos Aqui para Ajudar</span>
            <h1 class="support-title">Fala Connosco</h1>
            <p class="support-description">
                Tens dúvidas sobre os teus <strong>pontos</strong>, problemas com o download de e-books ou queres sugerir novas missões literárias? Envia-nos uma mensagem!
            </p>
            
        
            <div class="contact-card">
                <div class="card-icon">✉️</div>
                <div class="card-text">
                    <span>Envia um E-mail</span>
                    <a href="mailto:suporte@bookhub.pt">suporte@bookhub.pt</a>
                </div>
            </div>
        
            <div class="contact-card">
                <div class="card-icon">📍</div>
                <div class="card-text">
                    <span>Sede Oficial</span>
                    <strong>Porto, Portugal</strong>
                </div>
            </div>
        </div>

        <!-- COLUNA DA DIREITA: Mascote + Formulário -->
        <div class="support-form-wrapper">
            
            <!-- Balão de Fala e Mascote (A Coruja) -->
            <div class="mascot-wrapper">
                <div class="mascot-bubble">
                    Algum problema com os teus Pontos? 🦉
                </div>
                <img src="imagens/coruja-oculos.png" alt="Mascote BookHub Coruja com Óculos" title="Mascote BookHub de óculos" class="mascot-img">
            </div>

          
            <div class="support-form-box support-form-box--transparent">
                <h2>Formulário de Suporte</h2>
                
               <form action="#" method="POST" class="needs-validation " novalidate>
                    <div class="row g-3">
                        
                        <div class="col-12 col-md-6">
                            <label for="nome" class="form-label small fw-semibold text-muted">Nome Completo</label>
                            <input type="text" class="form-control" id="nome" name="nome" placeholder="Ex: João Silva" required>
                        </div>
           
                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label small fw-semibold text-muted">Endereço de E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="nome@exemplo.com" required>
                        </div>

                        <div class="col-12">
                            <label for="assunto" class="form-label small fw-semibold text-muted">Motivo do Contacto</label>
                            <select class="form-select" id="assunto" name="assunto" required>
                                <option value="" selected disabled>Escolha uma opção...</option>
                                <option value="pontos">Dúvidas sobre o Saldo de Pontos</option>
                                <option value="download">Problemas no Download de E-books</option>
                                <option value="envio">Estado do Envio de Livro Físico</option>
                                <option value="outro">Outros Assuntos / Sugestões</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="mensagem" class="form-label small fw-semibold text-muted">A tua Mensagem</label>
                            <textarea class="form-control" id="mensagem" name="mensagem" rows="5" placeholder="Escreve aqui os detalhes da tua dúvida ou problema..." required></textarea>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-secondary w-100 py-3 fw-semibold">
                                <i class="fa-solid fa-paper-plane me-2"></i> Enviar Mensagem
                            </button>
                        </div>

                    </div>
                </form>
                
                
            </div>
        </div>

    </div>
</section>

</main>

<?php 
include 'includes/footer.php'; 
?>

<!-- Script de Validação nativo do Bootstrap 5 (Impede envios de campos vazios) -->
<script>
(() => {
  'use strict'
  const forms = document.querySelectorAll('.needs-validation')
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()
</script>
