<?php
// usuarios_excluir.php
include 'verificar_login.php';
include("../connections/db_connect.php");

$database_conn = "TransportePublico_ti19";
mysqli_select_db($conn, $database_conn);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    
    $id_usuario = $conn->real_escape_string($_GET['id']);
    
    // SEGURANÇA: Verificar se o usuário não está tentando excluir a si mesmo
    // Buscamos o email do usuário que será excluído
    $sql_busca = "SELECT email FROM tbusuarios WHERE id_usuario = '$id_usuario'";
    $result_busca = $conn->query($sql_busca);
    $user_alvo = $result_busca->fetch_assoc();

    // Comparamos com o email da sessão atual
    if ($user_alvo && $user_alvo['email'] == $_SESSION['login_usuario']) {
        // Impede a auto-exclusão
        $msg_erro = "Você não pode excluir seu próprio usuário enquanto está logado!";
        header("Location: usuarios_lista.php?msg_erro=" . urlencode($msg_erro));
        exit();
    }

    // Executa a exclusão
    $sql_delete = "DELETE FROM tbusuarios WHERE id_usuario = '$id_usuario'";
    
    if ($conn->query($sql_delete) === TRUE) {
        header("Location: usuarios_lista.php?msg=" . urlencode("Usuário excluído com sucesso!"));
    } else {
        header("Location: usuarios_lista.php?msg_erro=" . urlencode("Erro ao excluir: " . $conn->error));
    }

} else {
    header("Location: usuarios_lista.php?msg_erro=" . urlencode("ID inválido."));
}

$conn->close();
?>