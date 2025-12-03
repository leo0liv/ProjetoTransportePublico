<?php
// Incluir o arquivo de verificação de login para proteger a página
include 'verificar_login.php';

// Incluir a conexão
include("../connections/db_connect.php");

// Variável para o nome do seu banco de dados (ajuste se necessário)
$database_conn = "TransportePublico_ti19";

// Variáveis Globais
$tabela         = "tbmotoristas";
$campo_filtro   = "id_motorista";
$motorista_atual = null;
$mensagem       = '';
$tipo_alerta    = '';
$id_motorista_filtro = null;


// 1. Lógica para identificar o Motorista (GET ou POST)
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_motorista_filtro = $conn->real_escape_string($_GET['id']);
} else if (isset($_POST['id_motorista']) && is_numeric($_POST['id_motorista'])) {
    $id_motorista_filtro = $conn->real_escape_string($_POST['id_motorista']);
}

// 2. Processar a ATUALIZAÇÃO (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && $id_motorista_filtro) {
    
    mysqli_select_db($conn, $database_conn);
    
    // Recebe dados do formulário
    $nome              = $conn->real_escape_string($_POST['nome']);
    $cpf               = $conn->real_escape_string($_POST['cpf']);
    $data_nascimento   = $conn->real_escape_string($_POST['data_nascimento']); 
    $telefone          = $conn->real_escape_string($_POST['telefone']);
    $foto_atual        = $conn->real_escape_string($_POST['foto_url_atual']); // Nome da foto antiga

    $nome_img = $foto_atual; // Por padrão, mantém a foto atual

    // 2.1. Tratamento da NOVA Imagem (se um arquivo foi enviado)
    if (isset($_FILES['foto_url']) && $_FILES['foto_url']['error'] == UPLOAD_ERR_OK) {
        
        // Define o diretório de destino
        $dir_destino = "../imagens/motoristas/"; 
        
        // Cria um nome único para o novo arquivo
        $nome_img = time() . '_' . basename($_FILES['foto_url']['name']);
        $tmp_img  = $_FILES['foto_url']['tmp_name'];
        
        // Move o novo arquivo
        if (move_uploaded_file($tmp_img, $dir_destino . $nome_img)) {
            // Se o upload foi bem-sucedido e existia uma foto antiga, tenta deletá-la
            if ($foto_atual && file_exists($dir_destino . $foto_atual)) {
                unlink($dir_destino . $foto_atual);
            }
        } else {
            // Se o upload falhar, mantém a foto antiga e exibe um aviso
            $nome_img = $foto_atual; 
            $mensagem .= " Aviso: Falha ao carregar nova foto. Mantendo a foto anterior.";
            $tipo_alerta = 'warning';
        }
    }
    
    // 2.2. Consulta SQL para atualização dos dados
    $updateSQL  = "
                    UPDATE " . $tabela . "
                    SET nome = '$nome',
                        cpf = '$cpf',
                        data_nascimento = '$data_nascimento',
                        telefone = '$telefone',
                        foto_url = '$nome_img'
                    WHERE " . $campo_filtro . " = '$id_motorista_filtro';
                    ";
    $resultado  = $conn->query($updateSQL);
 
    if ($resultado) {
        $mensagem = "Motorista **$nome** atualizado com sucesso!" . $mensagem; // Adiciona o aviso de foto, se houver
        $tipo_alerta = $tipo_alerta ?: 'success'; // Prioriza aviso de erro sobre sucesso
    } else {
        if ($conn->errno == 1062) {
             $mensagem = "Erro: O CPF **$cpf** já existe no sistema.";
             $tipo_alerta = 'danger';
        } else {
             $mensagem = "Erro ao atualizar o motorista: " . $conn->error;
             $tipo_alerta = 'danger';
        }
    }
}
 
// 3. Consulta para buscar os dados ATUAIS do motorista (para preencher o formulário)
if ($id_motorista_filtro) {
    mysqli_select_db($conn, $database_conn);
    
    $consulta_motorista = "
                    SELECT *
                    FROM    " . $tabela . "
                    WHERE " . $campo_filtro . " = '$id_motorista_filtro';
                    ";
    $lista_motorista = $conn->query($consulta_motorista);
    
    if ($lista_motorista && $lista_motorista->num_rows == 1) {
        $motorista_atual = $lista_motorista->fetch_assoc();
        mysqli_free_result($lista_motorista);
    } else {
        $mensagem = "Erro: Motorista não encontrado.";
        $tipo_alerta = 'danger';
        $id_motorista_filtro = null; 
    }
}


// Se não houver ID válido, redireciona para a lista
if (!$id_motorista_filtro && !$mensagem) {
    header("Location: motoristas_lista.php?msg_erro=" . urlencode("ID do motorista não fornecido."));
    exit();
}

$conn->close();

$titulo_pagina = "Editar Motorista";
include '../admin/header.php'; 
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <h2 class="text-info mb-4">
                <i class="bi bi-person-badge-fill"></i> Editar Motorista
            </h2>
            
            <?php if ($mensagem): ?>
                <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
                    <?php echo $mensagem; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($motorista_atual): ?>
            <div class="card p-4 shadow">
                <form 
                    action="motoristas_atualiza.php"
                    method="post"
                    enctype="multipart/form-data" 
                    id="form_motorista_atualiza"
                    name="form_motorista_atualiza"
                >
                    <input type="hidden" name="id_motorista" value="<?php echo $motorista_atual['id_motorista']; ?>">
                    
                    <input type="hidden" name="foto_url_atual" value="<?php echo $motorista_atual['foto_url']; ?>">

                    
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
                                value="<?php echo htmlspecialchars($motorista_atual['nome']); ?>"
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
                                value="<?php echo htmlspecialchars($motorista_atual['cpf']); ?>"
                            >
                        </div> 
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
                                value="<?php echo htmlspecialchars($motorista_atual['telefone']); ?>"
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
                                value="<?php echo htmlspecialchars($motorista_atual['data_nascimento']); ?>"
                            >
                        </div> 
                    </div> 

                    <div class="mb-3">
                        <label class="form-label">Foto Atual:</label>
                        <br>
                        <?php if ($motorista_atual['foto_url']): ?>
                            <img 
                                src="../imagens/motoristas/<?php echo htmlspecialchars($motorista_atual['foto_url']); ?>" 
                                alt="Foto Atual"
                                class="img-thumbnail mb-2"
                                style="width: 150px; height: 150px; object-fit: cover;"
                            >
                            <small class="form-text text-muted d-block">Deixe o campo abaixo vazio para manter esta foto.</small>
                        <?php else: ?>
                             <p class="text-muted">Nenhuma foto cadastrada.</p>
                        <?php endif; ?>

                        <label for="foto_url" class="form-label mt-3">Substituir Foto:</label>
                        <input 
                            type="file" 
                            name="foto_url" 
                            id="foto_url"
                            class="form-control"
                            accept="image/*"
                        >
                    </div> 

                    <div class="d-flex justify-content-between pt-3">
                         <button 
                            type="submit" 
                            name="enviar"
                            id="enviar"
                            class="btn btn-info text-white"
                         >
                            <i class="bi bi-arrow-repeat"></i> Atualizar Motorista
                         </button>
                         <a href="motoristas_lista.php" class="btn btn-secondary">
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