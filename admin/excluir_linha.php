<?php
// OBRIGATÓRIO: Proteção de Login
include 'verificar_admin.php'; // Protegido só para admins
 
// Incluir a conexão
include("../connections/db_connect.php");
 
$database_conn = "TransportePublico_ti19";
 
// Selecionar o banco de dados
mysqli_select_db($conn, $database_conn);
 
// Verifica se o ID da linha foi fornecido na URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_linha = $conn->real_escape_string($_GET['id']);
 
    // VERIFICAÇÃO DE ASSOCIAÇÕES (CHAVE ESTRANGEIRA) ---
    // Verifica se existem veículos associados a esta linha
    $sql_check_veiculos = "SELECT COUNT(*) AS total FROM tbveiculos WHERE id_linha = '$id_linha'";
    $res_veiculos = $conn->query($sql_check_veiculos);
    $total_veiculos = $res_veiculos->fetch_assoc()['total'];
    // CORREÇÃO: Verifica se existem horários/viagens programadas (que contém as rotas)
    $sql_check_rotas = "SELECT COUNT(*) AS total FROM tbhorario_programados WHERE id_linha = '$id_linha'";
    $res_rotas = $conn->query($sql_check_rotas);
    $total_rotas = $res_rotas->fetch_assoc()['total'];
 
    
    // LÓGICA DE AVISO (SE HOUVER ASSOCIAÇÕES) ---
    if ($total_veiculos > 0 || $total_rotas > 0) {
        $msg_erro = "A Linha não pode ser excluída!";
        $msg_erro .= " Motivo:";
        if ($total_veiculos > 0) {
            $msg_erro .= " Possui $total_veiculos veículo(s) associado(s).";
        }
        if ($total_rotas > 0) {
            $msg_erro .= " Possui horários e rotas programadas.";
        }
        header("Location: linhas.php?msg_erro=" . urlencode($msg_erro));
        exit();
    }
    // EXECUÇÃO DO DELETE ---
    $sql_delete = "DELETE FROM tblinhas WHERE id_linha = '$id_linha'";
    if ($conn->query($sql_delete) === TRUE) {
        header("Location: linhas.php?msg=" . urlencode("Linha excluída com sucesso!"));
        exit();
    } else {
        $msg_erro = "Erro ao excluir a linha: " . $conn->error;
        header("Location: linhas.php?msg_erro=" . urlencode($msg_erro));
        exit();
    }
} else {
    header("Location: linhas.php?msg_erro=" . urlencode("ID da linha inválido."));
    exit();
}
 
$conn->close(); 
?>