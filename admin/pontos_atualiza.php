<?php
// Incluir o arquivo de verificação de login para proteger a página
include 'verificar_login.php';

// Incluir a conexão
include("../connections/db_connect.php");

// Variável para o nome do seu banco de dados (ajuste se necessário)
$database_conn = "TransportePublico_ti19";

// Variáveis Globais
$tabela         = "tbpontos";
$campo_filtro   = "id_ponto";
$ponto_atual    = null;
$mensagem       = '';
$tipo_alerta    = '';
$id_ponto_filtro = null;


// 1. Lógica para identificar o Ponto (GET ou POST)
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_ponto_filtro = $conn->real_escape_string($_GET['id']);
} else if (isset($_POST['id_ponto']) && is_numeric($_POST['id_ponto'])) {
    $id_ponto_filtro = $conn->real_escape_string($_POST['id_ponto']);
}

// 2. Processar a ATUALIZAÇÃO (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && $id_ponto_filtro) {
    
    mysqli_select_db($conn, $database_conn);
 
    // Recebe e sanitiza os dados do formulário
    $nome           = $conn->real_escape_string($_POST['nome']);
    $latitude       = floatval($_POST['latitude']);
    $longitude      = floatval($_POST['longitude']);
 
    // Consulta SQL para atualização dos dados
    $updateSQL  = "
                    UPDATE " . $tabela . "
                    SET nome = '$nome',
                        latitude = '$latitude',
                        longitude = '$longitude'
                    WHERE " . $campo_filtro . " = '$id_ponto_filtro';
                    ";
    $resultado  = $conn->query($updateSQL);
 
    if ($resultado) {
        $mensagem = "Ponto **$nome** atualizado com sucesso!";
        $tipo_alerta = 'success';
    } else {
        $mensagem = "Erro ao atualizar o ponto: " . $conn->error;
        $tipo_alerta = 'danger';
    }
}
 
// 3. Consulta para buscar os dados ATUAIS do ponto (para preencher o formulário)
if ($id_ponto_filtro) {
    mysqli_select_db($conn, $database_conn);
    
    $consulta_ponto = "
                    SELECT *
                    FROM    " . $tabela . "
                    WHERE " . $campo_filtro . " = '$id_ponto_filtro';
                    ";
    $lista_ponto = $conn->query($consulta_ponto);
    
    if ($lista_ponto && $lista_ponto->num_rows == 1) {
        $ponto_atual = $lista_ponto->fetch_assoc();
        mysqli_free_result($lista_ponto);
    } else {
        $mensagem = "Erro: Ponto não encontrado.";
        $tipo_alerta = 'danger';
        $id_ponto_filtro = null; 
    }
}


// Se não houver ID válido, redireciona para a lista (a menos que já haja mensagem de erro)
if (!$id_ponto_filtro && !$mensagem) {
    header("Location: pontos_lista.php?msg_erro=" . urlencode("ID do ponto não fornecido."));
    exit();
}

$conn->close();

$titulo_pagina = "Editar Ponto de Ônibus";
include '../admin/header.php'; 
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            
            <h2 class="text-info mb-4">
                <i class="bi bi-pencil-fill"></i> Editar Ponto de Ônibus
            </h2>
            
            <?php if ($mensagem): ?>
                <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
                    <?php echo $mensagem; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($ponto_atual): ?>
            <div class="card p-4 shadow">
                <form 
                    action="pontos_atualiza.php"
                    method="post"
                    id="form_ponto_atualiza"
                    name="form_ponto_atualiza"
                >
                    <input type="hidden" name="id_ponto" value="<?php echo $ponto_atual['id_ponto']; ?>">

                    
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Ponto:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-signpost-2-fill"></i></span>
                            <input 
                                type="text" 
                                name="nome" 
                                id="nome"
                                class="form-control"
                                placeholder="Ex: Terminal Rodoviário Central"
                                maxlength="100"
                                required
                                value="<?php echo htmlspecialchars($ponto_atual['nome']); ?>"
                            >
                        </div> 
                    </div> 
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="latitude" class="form-label">Latitude:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-cursor-fill"></i></span>
                                <input 
                                    type="number"
                                    name="latitude"
                                    id="latitude"
                                    class="form-control"
                                    min="-90"
                                    max="90"
                                    step="any"
                                    required
                                    placeholder="Ex: -23.5850"
                                    value="<?php echo htmlspecialchars($ponto_atual['latitude']); ?>"
                                >
                            </div> 
                            <small class="form-text text-muted">Ex: -23.5850.</small>
                        </div> 
                        
                        <div class="col-md-6 mb-3">
                            <label for="longitude" class="form-label">Longitude:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-ruler-fill"></i></span>
                                <input 
                                    type="number"
                                    name="longitude"
                                    id="longitude"
                                    class="form-control"
                                    min="-180"
                                    max="180"
                                    step="any"
                                    required
                                    placeholder="Ex: -48.0450"
                                    value="<?php echo htmlspecialchars($ponto_atual['longitude']); ?>"
                                >
                            </div> 
                            <small class="form-text text-muted">Ex: -48.0450.</small>
                        </div> 
                    </div>

                    <div class="d-flex justify-content-between pt-3">
                         <button 
                            type="submit" 
                            name="enviar"
                            id="enviar"
                            class="btn btn-info text-white"
                         >
                            <i class="bi bi-arrow-repeat"></i> Atualizar Ponto
                         </button>
                         <a href="pontos_lista.php" class="btn btn-secondary">
                             <i class="bi bi-arrow-left-circle-fill"></i> Voltar para a Lista
                         </a>
                    </div>
                </form>
            </div> <?php endif; ?>

        </div>
    </div>
</div>

<?php 
include '../admin/footer.php'; 
?>