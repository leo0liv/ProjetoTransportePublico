<?php
// verificar_login.php
session_start();

// Verifica se a sessão 'logado' NÃO está definida ou se é FALSE
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== TRUE) {
    // Se não estiver logado, destrói a sessão atual e redireciona
    session_unset();
    session_destroy();
    
    // Redireciona
    header("Location: login.php");
    exit();
}
// Se chegou aqui, o usuário está logado e o script continua
?>