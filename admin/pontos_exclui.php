<?php
// Incluir o arquivo de verificação de login para proteger a página
include 'verificar_login.php';

// Incluir a conexão
include("../connections/db_connect.php");

// Variável para o nome do seu banco de dados (ajuste se necessário)
//$database_conn = "TransportePublico_ti19";

// Selecionar o banco de dados (USE)
mysqli_select_db($conn, $database_conn);

// Verifica se o ID do ponto foi fornecido na URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    
    $id_ponto = $conn->real_escape_string($_GET['id']);
    
    // Consulta DELETE
    $sql = "DELETE FROM tbpontos WHERE id_ponto = '$id_ponto'";
    
    $resultado = $conn->query($sql);
    
    if ($resultado === TRUE) {
        // Redireciona de volta para a lista com uma mensagem de sucesso
        header("Location: pontos_lista.php?msg=" . urlencode("Ponto excluído com sucesso!"));
        exit();
    } else {
        // Trata erro de chave estrangeira (Ponto ainda está em uso na tabela tbrotas)
        if ($conn->errno == 1451) {
            $msg_erro = "Erro: O ponto não pode ser excluído, pois está associado a uma ROTA. Remova-o da rota primeiro.";
        } else {
            $msg_erro = "Erro ao excluir o ponto: " . $conn->error;
        }
        
        // Redireciona com uma mensagem de erro
        header("Location: pontos_lista.php?msg_erro=" . urlencode($msg_erro));
        exit();
    }
} else {
    // Se não houver ID válido, redireciona para a lista
    header("Location: pontos_lista.php?msg_erro=" . urlencode("ID do ponto inválido ou não fornecido."));
    exit();
}

$conn->close(); 
?>