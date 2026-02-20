<?php
// OBRIGATÓRIO: Proteção de Login
include 'verificar_login.php'; 

// Incluir a conexão
include("../connections/db_connect.php");

$database_conn = "TransportePublico_ti19";
$mensagem = '';
$tipo_alerta = '';

// Lógica de Inserção (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    mysqli_select_db($conn, $database_conn);

    $tabela_insert  =   "tblinhas";
    $campos_insert  =   "codigo, nome, operadora";
    
    // Recebe e sanitiza os dados do formulário
    $codigo         =   $conn->real_escape_string(strtoupper($_POST['codigo']));
    $nome           =   $conn->real_escape_string($_POST['nome']);
    $operadora      =   $conn->real_escape_string($_POST['operadora']);     

    // Query de Inserção
    $insertSQL  =   "
                    INSERT INTO ".$tabela_insert."
                        (".$campos_insert.")
                    VALUES
                        ('$codigo', '$nome', '$operadora');
                    ";
    $resultado  =   $conn->query($insertSQL);

    $destino    =   "linhas.php";
    if($resultado){
        $mensagem = "Linha **$nome ($codigo)** cadastrada com sucesso!";
        $tipo_alerta = 'success';
    } else {
        if ($conn->errno == 1062) {
             $mensagem = "Erro: O código **$codigo** já existe no sistema.";
             $tipo_alerta = 'warning';
        } else {
             $mensagem = "Erro ao cadastrar linha: " . $conn->error;
             $tipo_alerta = 'danger';
        }
    };
};

$conn->close();

$titulo_pagina = "Inserir Linha";
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
        <div class="col-md-6">
            
            <h2 class="text-primary mb-4">
                <i class="bi bi-plus-circle-fill"></i> Cadastrar Nova Linha
            </h2>
            
            <?php if ($mensagem): ?>
                <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
                    <?php echo $mensagem; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card p-4 shadow">
                <form 
                    action="adicionar_linha.php"
                    method="post"
                    id="form_linha_insere"
                    name="form_linha_insere"
                >
                    
                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código da Linha:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-tag-fill"></i></span>
                            <input 
                                type="text" 
                                name="codigo" 
                                id="codigo"
                                class="form-control"
                                placeholder="Ex: 101-A"
                                maxlength="20"
                                required
                            >
                        </div> 
                    </div> 

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome da Linha:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-road"></i></span>
                            <input 
                                type="text" 
                                name="nome" 
                                id="nome"
                                class="form-control"
                                placeholder="Terminal - Vila Rio Branco"
                                maxlength="100"
                                required
                            >
                        </div> 
                    </div> 

                    <div class="mb-3">
                        <label for="operadora" class="form-label">Operadora:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-briefcase-fill"></i></span>
                            <input 
                                type="text"
                                name="operadora"
                                id="operadora"
                                class="form-control"
                                placeholder="Viação Cidade"
                                maxlength="100"
                            >
                        </div> 
                    </div> 

                    <div class="d-flex justify-content-between">
                         <button 
                            type="submit" 
                            name="enviar"
                            id="enviar"
                            class="btn btn-primary"
                         >
                            <i class="bi bi-save-fill"></i> Cadastrar Linha
                         </button>
                         <a href="linhas.php" class="btn btn-secondary">
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
</body>
