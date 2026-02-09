<?php
// OBRIGATÓRIO: Esta linha garante que, se o login for bem-sucedido, 
// o usuário possa entrar. Se ele não estiver logado, será enviado de volta para login.php.
include '../admin/verificar_login.php'; 

// Inclua aqui seu header.php (com as tags iniciais do HTML)
include '../admin/header.php'; 

$titulo_pagina = "Painel Administrativo";
?>

<main>
    <?php include '../page_inicial.php';  ?>
</main>

<?php 
include 'footer.php'; 
?>