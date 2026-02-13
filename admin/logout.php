<?php
session_name("transporte_publico");
session_start();

// Limpa todas as variáveis de sessão
$_SESSION = array();

// Remove o cookie da sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destrói a sessão
session_destroy();

// Redireciona
header("Location: ../index.php");
exit;
?>