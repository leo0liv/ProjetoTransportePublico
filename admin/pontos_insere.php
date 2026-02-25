<?php
// Incluir o arquivo de verificação de login
include 'verificar_login.php';

// Incluir a conexão
include("../connections/db_connect.php");

//$database_conn = "TransportePublico_ti19";
$mensagem = '';
$tipo_alerta = '';

// LÓGICA DE INSERÇÃO
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    mysqli_select_db($conn, $database_conn);

    // Recebe e sanitiza os dados
    $nome       = $conn->real_escape_string($_POST['nome']);
    $latitude   = floatval($_POST['latitude']);
    $longitude  = floatval($_POST['longitude']);     

    // Query de Inserção limpa
    $insertSQL  = "INSERT INTO tbpontos (nome, latitude, longitude) VALUES ('$nome', '$latitude', '$longitude')";
    
    $resultado  = $conn->query($insertSQL);

    if($resultado){
        $mensagem = "Ponto <strong>$nome</strong> cadastrado com sucesso!";
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
        <div class="col-md-7">
            
            <h2 class="text-success mb-4">
                <i class="bi bi-geo-alt-fill"></i> Cadastrar Novo Ponto
            </h2>
            
            <?php if ($mensagem): ?>
                <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
                    <?php echo $mensagem; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card p-4 shadow">
                <form action="pontos_insere.php" method="post">   

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Local:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-signpost-2-fill"></i></span>
                            <input 
                                type="text" 
                                name="nome" 
                                class="form-control"
                                placeholder="Ex: Praça da Matriz"
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
                                    class="form-control"
                                    min="-90"
                                    max="90"
                                    step="any"
                                    required
                                    placeholder="Ex: -23.5850"
                                >
                            </div> 
                            <small class="text-muted">Use ponto para decimais.</small>
                        </div> 
                        
                        <div class="col-md-6 mb-3">
                            <label for="longitude" class="form-label">Longitude:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-ruler-fill"></i></span>
                                <input 
                                    type="number"
                                    name="longitude"
                                    class="form-control"
                                    min="-180"
                                    max="180"
                                    step="any"
                                    required
                                    placeholder="Ex: -48.0450"
                                >
                            </div> 
                        </div> 
                    </div>

                    <div class="d-flex justify-content-between pt-3">
                         <button type="submit" class="btn btn-success">
                            <i class="bi bi-save-fill"></i> Salvar Ponto
                         </button>
                         <a href="pontos_lista.php" class="btn btn-secondary">
                             <i class="bi bi-arrow-left-circle-fill"></i> Voltar
                         </a>
                    </div>
                </form>
            </div> 
        </div>
    </div>
</div>

<?php include '../admin/footer.php'; ?>
</body>