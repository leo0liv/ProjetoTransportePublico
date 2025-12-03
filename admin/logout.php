<?php
// OBRIGATÓRIO: Inicia a sessão para poder manipulá-la
session_start();

// 1. Limpa todas as variáveis de sessão registradas (dados do usuário)
session_unset();

// 2. Destrói a sessão atual (encerra a sessão no servidor)
session_destroy();

// 3. Redireciona o usuário para a página de login
header("Location: login.php");

// 4. Garante que o script pare de ser executado após o redirecionamento
exit();
?>