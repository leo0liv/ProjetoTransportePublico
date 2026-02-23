<?php
// OBRIGATÓRIO: Proteção de Login
include 'verificar_login.php';

// Incluir a conexão
include("../connections/db_connect.php");

$database_conn = "TransportePublico_ti19";

// Selecionar o banco de dados (USE)
mysqli_select_db($conn, $database_conn);

// Verifica se o ID da linha foi fornecido na URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    
    $id_linha = $conn->real_escape_string($_GET['id']);

    // VERIFICAÇÃO DE ASSOCIAÇÕES (CHAVE ESTRANGEIRA)
    
    // Verifica se existem veículos associados a esta linha
    $sql_check_veiculos = "SELECT COUNT(*) AS total FROM tbveiculos WHERE id_linha = '$id_linha'";
    $res_veiculos = $conn->query($sql_check_veiculos);
    $total_veiculos = $res_veiculos->fetch_assoc()['total'];
    
    // Verifica se existem rotas associadas a esta linha
    $sql_check_rotas = "SELECT COUNT(*) AS total FROM tbrotas WHERE id_linha = '$id_linha'";
    $res_rotas = $conn->query($sql_check_rotas);
    $total_rotas = $res_rotas->fetch_assoc()['total'];

    
    // LÓGICA DE AVISO (SE HOUVER ASSOCIAÇÕES)
    
    if ($total_veiculos > 0 || $total_rotas > 0) {
        $msg_erro = "A Linha não pode ser excluída!";
        $msg_erro .= " Motivo:";
        if ($total_veiculos > 0) {
            $msg_erro .= " Possui $total_veiculos veículo(s) associado(s).";
        }
        if ($total_rotas > 0) {
            $msg_erro .= " Possui $total_rotas ponto(s) em sua rota.";
        }
        
        // Redireciona com uma mensagem de ERRO AMIGÁVEL
        header("Location: linhas.php?msg_erro=" . urlencode($msg_erro));
        exit();
    }
    
    // EXECUÇÃO DO DELETE (SE NÃO HOUVER ASSOCIAÇÕES)
    
    // Consulta DELETE
    $sql_delete = "DELETE FROM tblinhas WHERE id_linha = '$id_linha'";
    
    $resultado = $conn->query($sql_delete);
    
    if ($resultado === TRUE) {
        // Redireciona de volta para a lista com uma mensagem de sucesso
        header("Location: linhas.php?msg=" . urlencode("Linha excluída com sucesso!"));
        exit();
    } else {
        // Erro SQL inesperado
        $msg_erro = "Erro ao excluir a linha: " . $conn->error;
        header("Location: linhas.php?msg_erro=" . urlencode($msg_erro));
        exit();
    }
} else {
    // Se não houver ID válido, redireciona para a lista
    header("Location: linhas.php?msg_erro=" . urlencode("ID da linha inválido ou não fornecido."));
    exit();
}

$conn->close(); 
?>