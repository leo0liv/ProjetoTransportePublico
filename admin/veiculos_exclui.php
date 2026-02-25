<?php
// Incluir o arquivo de verificação de login para proteger a página
include 'verificar_login.php';

// Incluir a conexão
include("../connections/db_connect.php");

// Variável para o nome do seu banco de dados (ajuste se necessário)
//$database_conn = "TransportePublico_ti19";

// Selecionar o banco de dados (USE)
mysqli_select_db($conn, $database_conn);

// Verifica se o ID do veículo foi fornecido na URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    
    $id_veiculo = $conn->real_escape_string($_GET['id']);
    
    // Consulta DELETE
    $sql = "DELETE FROM tbveiculos WHERE id_veiculo = '$id_veiculo'";
    
    $resultado = $conn->query($sql);
    
    if ($resultado === TRUE) {
        // Redireciona de volta para a lista com uma mensagem de sucesso
        header("Location: veiculos_lista.php?msg=" . urlencode("Veículo excluído com sucesso!"));
        exit();
    } else {
        // ATENÇÃO: Se o veículo estiver associado à tabela 'tblocalizacao_tempo_real'
        // ou 'tbprevisao_chegada' (chaves estrangeiras), a exclusão será barrada.
        
        $msg_erro = "Erro ao excluir o veículo. Verifique se não há dados de localização ou previsão de chegada associados a ele.";
        
        // Redireciona com uma mensagem de erro
        header("Location: veiculos_lista.php?msg_erro=" . urlencode($msg_erro));
        exit();
    }
} else {
    // Se não houver ID válido, redireciona para a lista
    header("Location: veiculos_lista.php?msg_erro=" . urlencode("ID do veículo inválido ou não fornecido."));
    exit();
}

$conn->close(); 
?>