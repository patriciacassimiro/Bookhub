// validacao de checkout e formolários

document.getElementById("formCheckout").addEventListener("submit", function(event) {
    const errorBox = document.getElementById("js-error-msg");
    errorBox.style.display = "none";
    errorBox.innerText = "";

    // Recolhe os valores dos campos
    const nome = document.getElementById("nome_envio").value.trim();
    const dataNascInput = document.getElementById("data_nascimento").value;
    const morada = document.getElementById("morada").value.trim();

    // Valida campos vazios
    if (nome === "" || dataNascInput === "" || morada === "") {
        event.preventDefault(); // Trava o envio
        showError("❌ Erro: Nenhum dos campos poderá ficar vazio.");
        return;
    }

    //  (Mínimo 18 anos)
    const dataNascimento = new Date(dataNascInput);
    const hoje = new Date();
    
    let idade = hoje.getFullYear() - dataNascimento.getFullYear();
    const diferencaMeses = hoje.getMonth() - dataNascimento.getMonth();
    
    // Ajusta o cálculo caso o utilizador ainda não tenha feito anos no mês atual
    if (diferencaMeses < 0 || (diferencaMeses === 0 && hoje.getDate() < dataNascimento.getDate())) {
        idade--;
    }

    if (idade < 18) {
        event.preventDefault();
        showError("⛔ Erro: O titular do envio deve ser maior de idade (igual ou superior a 18 anos).");
        return;
    }

    // Proteção de script maliciosos
    const regexScript = /<script\b[^>]*>([\s\S]*?)<\/script>/gi;
    if (regexScript.test(nome) || regexScript.test(morada)) {
        event.preventDefault();
        showError("⚠️ Alerta de Segurança: Tentativa de injeção de código detetada.");
        return;
    }
});

function showError(mensagem) {
    const errorBox = document.getElementById("js-error-msg");
    errorBox.innerText = mensagem;
    errorBox.style.display = "block";
    window.scrollTo({ top: 0, behavior: 'smooth' }); // Sobe o ecrã até o aviso
}


// Validação do formulário de registo na página autenticacao.php
document.addEventListener("DOMContentLoaded", function() {
    const formRegisto = document.querySelector("form[action='processar-registo.php']");
    
    if (formRegisto) {
        formRegisto.addEventListener("submit", function(event) {
            const senhaInput = document.getElementById("reg_senha").value;
            const errorBox = document.getElementById("js-error-msg");
            
            // EXPRESSÃO REGULAR: Mínimo 6 caracteres, 1 Maiúscula, 1 Minúscula, 1 Número, 1 Especial
            const regexSenha = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.])[A-Za-z\d@$!%*?&.]{6,}$/;

            if (!regexSenha.test(senhaInput)) {
                event.preventDefault();
                
                if (errorBox) {
                    errorBox.innerText = "❌ Erro na Senha: Deve ter pelo menos 6 caracteres, incluindo 1 letra maiúscula, 1 letra minúscula, 1 número e 1 caractere especial.";
                    errorBox.style.display = "block";
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    alert("🔒 Erro na Senha:\n\nA senha não cumpre os requisitos mínimos exibidos no site!");
                }
            }
        });
    }
});
