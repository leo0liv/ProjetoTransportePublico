<?php
// Incluir o arquivo de verificação de login para proteger a página
include 'verificar_login.php';

// Incluir a conexão
include("../connections/db_connect.php");

// Variável para o nome do seu banco de dados (ajuste se necessário)
//$database_conn = "TransportePublico_ti19";

// Selecionar o banco de dados (USE)
mysqli_select_db($conn, $database_conn);

// Verifica se o ID do motorista foi fornecido na URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    
    $id_motorista = $conn->real_escape_string($_GET['id']);
    
    // Consulta DELETE
    $sql = "DELETE FROM tbmotoristas WHERE id_motorista = '$id_motorista'";
    
    $resultado = $conn->query($sql);
    
    if ($resultado === TRUE) {
        // Redireciona de volta para a lista com uma mensagem de sucesso
        header("Location: motoristas_lista.php?msg=" . urlencode("Motorista excluído com sucesso!"));
        exit();
    } else {
        // Trata erro de chave estrangeira (Motorista ainda está alocado)
        if ($conn->errno == 1451) {
            $msg_erro = "Erro: O motorista não pode ser excluído, pois está ALOCADO em um veículo ou possui registros de alocação. Remova as alocações primeiro.";
        } else {
            $msg_erro = "Erro ao excluir o motorista: " . $conn->error;
        }
        
        // Redireciona com uma mensagem de erro
        header("Location: motoristas_lista.php?msg_erro=" . urlencode($msg_erro));
        exit();
    }
} else {
    // Se não houver ID válido, redireciona para a lista
    header("Location: motoristas_lista.php?msg_erro=" . urlencode("ID do motorista inválido ou não fornecido."));
    exit();
}

$conn->close(); 
?>