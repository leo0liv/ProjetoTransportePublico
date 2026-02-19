<?php
// verificar_login.php

// 1. VERIFICAÇÃO INTELIGENTE DA SESSÃO
// Só inicia a sessão se ela ainda NÃO estiver ativa.
// Isso elimina os erros de "Warning" e "Notice" da sua tela.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name("transporte_publico");
    session_start();
}

// 2. Verifica se a variável 'login_usuario' existe
if (!isset($_SESSION['login_usuario'])) {
    // Se não estiver logado, limpa tudo e chuta para o login
    session_unset();
    session_destroy();
    
    // Redireciona para o login
    header("Location: login.php");
    exit();
}

// 3. (Opcional) Segurança extra
// session_regenerate_id(true); 
?>