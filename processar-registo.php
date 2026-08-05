<?php
session_start();

include 'includes/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura e limpa os dados enviados para evitar falhas de segurança e SQL Injection
    $nome  = mysqli_real_escape_string($conexao, $_POST['nome']);
    $email = mysqli_real_escape_string($conexao, $_POST['email']);
    $senha = $_POST['senha'];

    // VALIDACÃO EXIGIDA: Verificar se a senha cumpre os requisitos de segurança

    $regex_senha = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.])[A-Za-z\d@$!%*?&.]{6,}$/';

    if (!preg_match($regex_senha, $senha)) {
        // Se a senha for fraca, expulsa de volta para a página de autenticação com um erro
        header("Location: autenticacao.php?erro=senha_fraca");
        exit();
    }

    // VERIFICAÇÃO: Validar se o e-mail já existe na base de dados
    $sql_verificar = "SELECT id FROM utilizadores WHERE email = '$email'";
    $resultado_verificar = mysqli_query($conexao, $sql_verificar);

    if (mysqli_num_rows($resultado_verificar) > 0) {
        header("Location: autenticacao.php?erro=email_existe");
        exit();
    }

    //  ENCRIPTAR A SENHA
    $senha_segura = password_hash($senha, PASSWORD_DEFAULT);

    // INSERIR O NOVO LEITOR NA BASE DE DADOS
    $sql_inserir = "INSERT INTO utilizadores (nome, email, senha, tipo, pontos, nivel, xp) 
                    VALUES ('$nome', '$email', '$senha_segura', 'leitor', 0, 1, 0)";
    
    if (mysqli_query($conexao, $sql_inserir)) {
        $novo_utilizador_id = mysqli_insert_id($conexao);

        // ATRIBUIR AS MISSÕES INICIAIS AO LEITOR AUTOMATICAMENTE
        $sql_desafios = "SELECT id FROM desafios";
        $resultado_desafios = mysqli_query($conexao, $sql_desafios);

        while ($desafio = mysqli_fetch_assoc($resultado_desafios)) {
            $desafio_id = $desafio['id'];
            mysqli_query($conexao, "INSERT INTO progresso_desafios (utilizador_id, desafio_id, estado) VALUES ($novo_utilizador_id, $desafio_id, 'em_progresso')");
        }

        $_SESSION['utilizador_id']     = $novo_utilizador_id;
        $_SESSION['utilizador_nome']   = $nome;
        $_SESSION['utilizador_tipo']   = 'leitor';
        $_SESSION['utilizador_pontos'] = 0;

        // Redireciona direto para o painel de gamificação
        header("Location: desafios.php");
        exit();

    } else {
        echo "Erro crítico na base de dados: " . mysqli_error($conexao);
    }
} else {
    header("Location: autenticacao.php");
    exit();
}
?>
