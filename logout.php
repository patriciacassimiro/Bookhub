<?php
session_start();

$_SESSION = array();

// destruir cookies ao terminar sessao
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// destruir o registo da sessão no servidor 
session_destroy();

//redireciona o utilizador para a página de login/registo
header("Location: autenticacao.php?info=sessao_encerrada");
exit();
?>
