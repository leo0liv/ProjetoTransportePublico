<?php
// Nome da sessão do projeto Transporte Público
session_name("transporte_publico");

if (!isset($_SESSION)) {
    session_start();
}

// Verifica se o usuário está logado
if (!isset($_SESSION['login_usuario'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Verifica integridade da sessão
if (!isset($_SESSION['nome_da_sessao']) || ($_SESSION['nome_da_sessao'] != session_name())) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>