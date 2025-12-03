<?php
$senha_clara = 'senhati19'; // Sua senha de teste
$hash_da_senha = password_hash($senha_clara, PASSWORD_DEFAULT);
echo "Senha clara: " . $senha_clara . "<br>";
echo "Hash gerado: **" . $hash_da_senha . "**";
?>