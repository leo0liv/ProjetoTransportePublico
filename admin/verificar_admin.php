<?php
// verificar_admin.php

// 1. Primeiro verifica se a pessoa está logada
include 'verificar_login.php';

// 2. Depois verifica se o nível é 'admin'
if (!isset($_SESSION['nivel_usuario']) || $_SESSION['nivel_usuario'] !== 'admin') {
    // Se não for admin, chuta para a página de "Sem Permissão"
    header("Location: invasor_user.php");
    exit();
}
?>