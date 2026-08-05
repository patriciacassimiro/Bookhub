<?php
session_start();

include 'includes/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Captura e limpa os dados enviados para evitar falhas de segurança e SQL Injection
    $email = mysqli_real_escape_string($conexao, $_POST['email']);
    $senha = $_POST['senha'];

    //  CONSULTA À BASE DE DADOS
    $sql = "SELECT * FROM utilizadores WHERE email = '$email'";
    $resultado = mysqli_query($conexao, $sql);

    if (mysqli_num_rows($resultado) == 1) {
        
        $utilizador = mysqli_fetch_assoc($resultado);

        //  VERIFICAÇÃO DE SEGURANÇA DA SENHA
        if (password_verify($senha, $utilizador['senha']) || $senha === $utilizador['senha']) {
            
            // Grava os dados essenciais na memória do navegador
            $_SESSION['utilizador_id']     = $utilizador['id'];
            $_SESSION['utilizador_nome']   = $utilizador['nome'];
            $_SESSION['utilizador_tipo']   = $utilizador['tipo']; // 'leitor' ou 'admin'
            $_SESSION['utilizador_pontos'] = $utilizador['pontos'];

            // Se o campo 'tipo' no Workbench for 'admin', abre o painel de administração
            if ($utilizador['tipo'] == 'admin') {
                header("Location: admin.php");
            } else {
                // Se for um leitor comum, vai direto para o painel de gamificação
                header("Location: desafios.php");
            }
            exit();
        }
    }

    // Se o e-mail não existir ou a senha estiver errada, volta para a página com aviso de erro
    header("Location: autenticacao.php?erro=dados_invalidos");
    exit();
} else {
    // Se alguém tentar aceder a este ficheiro diretamente pela URL (sem submeter o formulário)
    header("Location: autenticacao.php");
    exit();
}
?>
