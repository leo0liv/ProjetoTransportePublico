<?php
// Incluir o arquivo de verificação de login para proteger a página
include 'verificar_login.php';

// Incluir a conexão
include("../connections/db_connect.php");

$database_conn = "TransportePublico_ti19";
$mensagem = '';
$tipo_alerta = '';

// Lógica de Inserção (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    mysqli_select_db($conn, $database_conn);

    $tabela_insert  =   "tbpontos";
    $campos_insert  =   "nome, latitude, longitude";
    
    // Recebe e sanitiza os dados
    $nome           =   $conn->real_escape_string($_POST['nome']);
    // As coordenadas são decimais (DECIMAL(10,6)), usamos floatval para garantir o formato correto
    $latitude       =   floatval($_POST['latitude']);
    $longitude      =   floatval($_POST['longitude']);     

    // Query de Inserção
    $insertSQL  =   "
                    INSERT INTO ".$tabela_insert."
                        (".$campos_insert.")
                    VALUES
                        ('$nome', '$latitude', '$longitude');
                    ";
    $resultado  =   $conn->query($insertSQL);

    $destino    =   "pontos_lista.php";
    if($resultado){
        $mensagem = "Ponto **$nome** cadastrado com sucesso!";
        $tipo_alerta = 'success';
    } else {
        $mensagem = "Erro ao cadastrar ponto: " . $conn->error;
        $tipo_alerta = 'danger';
    };
};

$conn->close();

$titulo_pagina = "Inserir Ponto de Ônibus";
include '../admin/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            
            <h2 class="text-primary mb-4">
                <i class="bi bi-geo-alt-fill"></i> Cadastrar Novo Ponto de Ônibus
            </h2>
            
            <?php if ($mensagem): ?>
                <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
                    <?php echo $mensagem; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card p-4 shadow">
                <form 
                    action="pontos_insere.php"
                    method="post"
                    id="form_ponto_insere"
                    name="form_ponto_insere"
                >
                    
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
                                >
                            </div> 
                            <small class="form-text text-muted">Ex: -23.5850 (até 6 casas decimais).</small>
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
                                >
                            </div> 
                            <small class="form-text text-muted">Ex: -48.0450 (até 6 casas decimais).</small>
                        </div> 
                    </div>

                    <div class="d-flex justify-content-between pt-3">
                         <button 
                            type="submit" 
                            name="enviar"
                            id="enviar"
                            class="btn btn-primary"
                         >
                            <i class="bi bi-save-fill"></i> Cadastrar Ponto
                         </button>
                         <a href="pontos_lista.php" class="btn btn-secondary">
                             <i class="bi bi-arrow-left-circle-fill"></i> Voltar
                         </a>
                    </div>
                </form>
            </div> </div>
    </div>
</div>

<?php 
include '../admin/footer.php'; 
?>