<?php
// Incluir o arquivo de verificação de login para proteger a página
include 'verificar_login.php';

// Incluir a conexão
include("../connections/db_connect.php");

// Variável para o nome do seu banco de dados
//$database_conn = "TransportePublico_ti19";

// Variáveis Globais
$tabela_veiculo  = "tbveiculos";
$tabela_linha    = "tblinhas";
$campo_filtro    = "id_veiculo";
$veiculo_atual   = null;
$lista_linhas    = null;
$mensagem        = '';
$tipo_alerta     = '';
$id_veiculo_filtro = null;


// Lógica para carregar os dados (GET ou POST)
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_veiculo_filtro = $conn->real_escape_string($_GET['id']);
} else if (isset($_POST['id_veiculo']) && is_numeric($_POST['id_veiculo'])) {
    $id_veiculo_filtro = $conn->real_escape_string($_POST['id_veiculo']);
}

// Processar a ATUALIZAÇÃO (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && $id_veiculo_filtro) {
    
    mysqli_select_db($conn, $database_conn);
 
    // Receber os dados do formulário
    $placa          = $conn->real_escape_string(strtoupper($_POST['placa']));
    $id_linha       = $conn->real_escape_string($_POST['id_linha']);
    $capacidade     = $conn->real_escape_string($_POST['capacidade']);
 
    // Consulta SQL para atualização dos dados
    $updateSQL  = "
                    UPDATE " . $tabela_veiculo . "
                    SET placa = '$placa',
                        id_linha = '$id_linha',
                        capacidade = '$capacidade'
                    WHERE " . $campo_filtro . " = '$id_veiculo_filtro';
                    ";
    $resultado  = $conn->query($updateSQL);
 
    if ($resultado) {
        $mensagem = "Veículo de placa **$placa** atualizado com sucesso!";
        $tipo_alerta = 'success';
    } else {
        if ($conn->errno == 1062) {
             $mensagem = "Erro: A placa **$placa** já existe no sistema.";
             $tipo_alerta = 'warning';
        } else {
             $mensagem = "Erro ao atualizar o veículo: " . $conn->error;
             $tipo_alerta = 'danger';
        }
    }
}
 
// Consulta para buscar os dados ATUAIS do veículo (para preencher o formulário)
if ($id_veiculo_filtro) {
    mysqli_select_db($conn, $database_conn);
    
    $consulta_veiculo = "
                    SELECT *
                    FROM    " . $tabela_veiculo . "
                    WHERE " . $campo_filtro . " = '$id_veiculo_filtro';
                    ";
    $lista_veiculo = $conn->query($consulta_veiculo);
    
    if ($lista_veiculo && $lista_veiculo->num_rows == 1) {
        $veiculo_atual = $lista_veiculo->fetch_assoc();
        mysqli_free_result($lista_veiculo);
    } else {
        $mensagem = "Erro: Veículo não encontrado.";
        $tipo_alerta = 'danger';
        $id_veiculo_filtro = null; // Impede a exibição do formulário
    }
}


// Consulta para buscar todas as Linhas (Chave Estrangeira)
mysqli_select_db($conn, $database_conn);
$consulta_linhas = "
                    SELECT id_linha, codigo, nome
                    FROM " . $tabela_linha . "
                    ORDER BY nome ASC;
                    ";
$lista_linhas = $conn->query($consulta_linhas);
$totalRows_linhas = $lista_linhas->num_rows;


// Se não houver ID válido, redireciona para a lista
if (!$id_veiculo_filtro && !$mensagem) {
    header("Location: veiculos_lista.php?msg_erro=" . urlencode("ID do veículo não fornecido."));
    exit();
}

$conn->close();

$titulo_pagina = "Editar Veículo";
include 'header.php'; 
?>
<style>
    body { 
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', sans-serif; 
        }
</style>
<body>
    <div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <h2 class="text-warning mb-4">
                <i class="bi bi-pencil-fill"></i> Editar Veículo
            </h2>
            
            <?php if ($mensagem): ?>
                <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
                    <?php echo $mensagem; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($veiculo_atual): ?>
            <div class="card p-4 shadow">
                <form 
                    action="veiculos_atualiza.php"
                    method="post"
                    id="form_veiculo_atualiza"
                    name="form_veiculo_atualiza"
                >
                    <input type="hidden" name="id_veiculo" value="<?php echo $veiculo_atual['id_veiculo']; ?>">

                    
                    <div class="mb-3">
                        <label for="id_linha" class="form-label">Linha Associada:</label>
                        <select 
                            name="id_linha" 
                            id="id_linha"
                            class="form-select"
                            required
                        >
                            <option value="">Selecione a Linha</option>
                            <?php if ($totalRows_linhas > 0): ?>
                                <?php while($row_linha = $lista_linhas->fetch_assoc()): ?>
                                    <option value="<?php echo $row_linha['id_linha']; ?>"
                                        <?php 
                                        // Marca a linha atual como selecionada
                                        if ($row_linha['id_linha'] == $veiculo_atual['id_linha']) {
                                            echo 'selected';
                                        }
                                        ?>
                                    >
                                        <?php echo $row_linha['codigo'] . ' - ' . $row_linha['nome']; ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                        <?php if ($totalRows_linhas == 0): ?>
                             <div class="alert alert-warning mt-2 p-2">
                                 <i class="bi bi-exclamation-triangle-fill"></i> Nenhuma linha cadastrada.
                             </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="placa" class="form-label">Placa do Veículo:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                            <input 
                                type="text" 
                                name="placa" 
                                id="placa"
                                class="form-control"
                                placeholder="Ex: ABC1B23"
                                maxlength="10"
                                required
                                value="<?php echo htmlspecialchars($veiculo_atual['placa']); ?>"
                            >
                        </div> 
                    </div>

                    <div class="mb-3">
                        <label for="capacidade" class="form-label">Capacidade (Passageiros):</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-people-fill"></i></span>
                            <input 
                                type="number"
                                name="capacidade"
                                id="capacidade"
                                class="form-control"
                                min="10"
                                max="150"
                                required
                                placeholder="Ex: 45"
                                value="<?php echo htmlspecialchars($veiculo_atual['capacidade']); ?>"
                            >
                        </div> 
                    </div>

                    <div class="d-flex justify-content-between">
                         <button 
                            type="submit" 
                            name="enviar"
                            id="enviar"
                            class="btn btn-warning text-white"
                         >
                            <i class="bi bi-arrow-repeat"></i> Atualizar Veículo
                         </button>
                         <a href="veiculos_lista.php" class="btn btn-secondary">
                             <i class="bi bi-arrow-left-circle-fill"></i> Voltar para a Lista
                         </a>
                    </div>
                </form>
            </div> <?php endif; ?>

        </div>
    </div>
</div>

<?php 
// Libera o resultado da consulta de linhas
if (isset($lista_linhas)) {
    mysqli_free_result($lista_linhas); 
}
include '../admin/footer.php'; 
?>
</body>