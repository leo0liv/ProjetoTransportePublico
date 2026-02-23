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
    
    $tabela_insert  = "tbmotoristas";
    $campos_insert  = "nome, cpf, foto_url, data_nascimento, telefone";
    
    // Tratamento da Imagem (simples upload, sem validação profunda)
    $nome_img = '';
    if (isset($_FILES['foto_url']) && $_FILES['foto_url']['error'] == UPLOAD_ERR_OK) {
        $nome_img = time() . '_' . basename($_FILES['foto_url']['name']); // Nome único
        $tmp_img  = $_FILES['foto_url']['tmp_name'];
        $dir_img  = "../imagens/motoristas/" . $nome_img; // Ajuste o diretório conforme seu projeto
        
        // Crie o diretório se ele não existir
        if (!is_dir("../imagens/motoristas")) {
            mkdir("../imagens/motoristas", 0777, true);
        }
        
        move_uploaded_file($tmp_img, $dir_img);
    }
    
    // Recebe e sanitiza os dados
    $nome              = $conn->real_escape_string($_POST['nome']);
    $cpf               = $conn->real_escape_string($_POST['cpf']);
    $data_nascimento   = $conn->real_escape_string($_POST['data_nascimento']); 
    $telefone          = $conn->real_escape_string($_POST['telefone']);     
    $foto_url          = $nome_img;

    // Query de Inserção
    $insertSQL  = "
                    INSERT INTO " . $tabela_insert . "
                        (" . $campos_insert . ")
                    VALUES
                        ('$nome', '$cpf', '$foto_url', '$data_nascimento', '$telefone');
                    ";
    $resultado  = $conn->query($insertSQL);

    $destino    = "motoristas_lista.php";
    if($resultado){
        $mensagem = "Motorista **$nome** cadastrado com sucesso!";
        $tipo_alerta = 'success';
    } else {
        if ($conn->errno == 1062) {
             $mensagem = "Erro: O CPF **$cpf** já existe no sistema. CPFs devem ser únicos.";
             $tipo_alerta = 'warning';
        } else {
             $mensagem = "Erro ao cadastrar motorista: " . $conn->error;
             $tipo_alerta = 'danger';
        }
    };
};

$conn->close();

$titulo_pagina = "Inserir Motorista";
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
        <div class="col-md-8">
            
            <h2 class="text-success mb-4">
                <i class="bi bi-person-plus-fill"></i> Cadastrar Novo Motorista
            </h2>
            
            <?php if ($mensagem): ?>
                <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
                    <?php echo $mensagem; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card p-4 shadow">
                <form 
                    action="motoristas_insere.php"
                    method="post"
                    enctype="multipart/form-data" 
                    id="form_motorista_insere"
                    name="form_motorista_insere"
                >
                    
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-lines-fill"></i></span>
                            <input 
                                type="text" 
                                name="nome" 
                                id="nome"
                                class="form-control"
                                placeholder="Ex: João da Silva"
                                maxlength="100"
                                required
                            >
                        </div> 
                    </div> 
                    
                    <div class="mb-3">
                        <label for="cpf" class="form-label">CPF:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-credit-card-2-front-fill"></i></span>
                            <input 
                                type="text" 
                                name="cpf" 
                                id="cpf"
                                class="form-control"
                                placeholder="Ex: 123.456.789-00"
                                maxlength="14"
                                required
                            >
                        </div> 
                        <small class="form-text text-muted">Apenas números ou formato padrão. O CPF deve ser único.</small>
                    </div> 

                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                            <input 
                                type="text" 
                                name="telefone" 
                                id="telefone"
                                class="form-control"
                                placeholder="Ex: (15) 99999-9999"
                                maxlength="20"
                            >
                        </div> 
                    </div> 
                    
                    <div class="mb-3">
                        <label for="data_nascimento" class="form-label">Data de Nascimento:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar-date-fill"></i></span>
                            <input 
                                type="date" 
                                name="data_nascimento" 
                                id="data_nascimento"
                                class="form-control"
                                required
                            >
                        </div> 
                    </div> 

                    <div class="mb-3">
                        <label for="foto_url" class="form-label">Foto do Motorista:</label>
                        <input 
                            type="file" 
                            name="foto_url" 
                            id="foto_url"
                            class="form-control"
                            accept="image/*"
                        >
                        <small class="form-text text-muted">A imagem será salva no diretório 'imagens/motoristas'.</small>
                    </div> 

                    <div class="d-flex justify-content-between pt-3">
                         <button 
                            type="submit" 
                            name="enviar"
                            id="enviar"
                            class="btn btn-success"
                         >
                            <i class="bi bi-save-fill"></i> Cadastrar Motorista
                         </button>
                         <a href="motoristas_lista.php" class="btn btn-secondary">
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