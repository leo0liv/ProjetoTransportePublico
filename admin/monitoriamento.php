<?php
/**
 * SISTEMA GESTÃO OPERACIONAL - PROCESSAMENTO DE TURNOS
 */

session_start();

// Configurações de Acesso
$database_conn = "TransportePublico_ti19";
$caminho_db    = "../connections/db_connect.php";

// Segurança: Só administradores podem processar
if (!isset($_SESSION['admin_logado']) && !isset($_SESSION['logado'])) {
    die("Acesso Negado: Sessão Inválida.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Verificar se a ligação existe
    if (file_exists($caminho_db)) {
        require_once($caminho_db);
    } else {
        die("Erro Crítico: Ficheiro de ligação ao banco de dados não encontrado.");
    }

    mysqli_select_db($conn, $database_conn);

    // Sanitização e Captura de Dados
    $id_motorista = filter_input(INPUT_POST, 'id_motorista', FILTER_SANITIZE_NUMBER_INT);
    $id_veiculo   = filter_input(INPUT_POST, 'id_veiculo', FILTER_SANITIZE_NUMBER_INT);
    $hora_inicio  = date("Y-m-d H:i:s");

    // Validação
    if (!$id_motorista || !$id_veiculo) {
        header("Location: monitoramentos.html?erro=campos_obrigatorios");
        exit();
    }

    // Execução (Transação Segura)
    try {
        // Exemplo de query real
        $sql = "INSERT INTO tbturnos (id_motorista, id_veiculo, hora_inicio, status) 
                VALUES (?, ?, ?, 'Ativo')";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $id_motorista, $id_veiculo, $hora_inicio);
        
        if ($stmt->execute()) {
            // Sucesso
            header("Location: monitoramentos.html?sucesso=turno_ativo");
        } else {
            throw new Exception($stmt->error);
        }

    } catch (Exception $e) {
        // Log de erro e redirecionamento
        header("Location: monitoramentos.html?erro=falha_sistema");
    }

    $stmt->close();
    $conn->close();

} else {
    // Tentativa de acesso direto
    header("Location: monitoramentos.html");
    exit();
}
?>