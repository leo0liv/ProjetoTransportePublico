<?php
// OBRIGATÓRIO: Esta linha garante que, se o login for bem-sucedido, 
// o usuário possa entrar. Se ele não estiver logado, será enviado de volta para login.php.
include '../admin/verificar_login.php'; 

// Inclua aqui seu header.php (com as tags iniciais do HTML)
include '../admin/header.php'; 

$titulo_pagina = "Painel Administrativo";
?>

<div class="container mt-5">
    <h1 class="text-primary mb-4">
        <i class="bi bi-speedometer2"></i> Painel de Controle
    </h1>
    
    </div>

<?php 
include 'footer.php'; 
?>