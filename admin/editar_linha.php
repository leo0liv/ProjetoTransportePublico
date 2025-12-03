<?php
// OBRIGATÓRIO: Proteção de Login
include 'verificar_login.php'; 

// Incluir a conexão
include("../connections/db_connect.php");

$database_conn = "TransportePublico_ti19";
$tabela         = "tblinhas";
$campo_filtro   = "id_linha";
$linha_atual    = null;
$mensagem       = '';
$tipo_alerta    = '';
$id_linha_filtro = null;


// 1. Lógica para identificar a Linha (GET ou POST)
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_linha_filtro = $conn->real_escape_string($_GET['id']);
} else if (isset($_POST['id_linha']) && is_numeric($_POST['id_linha'])) {
    $id_linha_filtro = $conn->real_escape_string($_POST['id_linha']);
}

// 2. Processar a ATUALIZAÇÃO (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && $id_linha_filtro) {
    
    mysqli_select_db($conn, $database_conn);
 
    $codigo         = $conn->real_escape_string(strtoupper($_POST['codigo']));
    $nome           = $conn->real_escape_string($_POST['nome']);
    $operadora      = $conn->real_escape_string($_POST['operadora']);
 
    // Consulta SQL para atualização dos dados
    $updateSQL  = "
                    UPDATE " . $tabela . "
                    SET codigo = '$codigo',
                        nome = '$nome',
                        operadora = '$operadora'
                    WHERE " . $campo_filtro . " = '$id_linha_filtro';
                    ";
    $resultado  = $conn->query($updateSQL);
 
    if ($resultado) {
        $mensagem = "Linha **$nome ($codigo)** atualizada com sucesso!";
        $tipo_alerta = 'success';
    } else {
        if ($conn->errno == 1062) {
             $mensagem = "Erro: O código **$codigo** já existe no sistema.";
             $tipo_alerta = 'warning';
        } else {
             $mensagem = "Erro ao atualizar a linha: " . $conn->error;
             $tipo_alerta = 'danger';
        }
    }
}
 
// 3. Consulta para buscar os dados ATUAIS da linha (para preencher o formulário)
if ($id_linha_filtro) {
    mysqli_select_db($conn, $database_conn);
    
    $consulta_linha = "
                    SELECT *
                    FROM    " . $tabela . "
                    WHERE " . $campo_filtro . " = '$id_linha_filtro';
                    ";
    $lista_linha = $conn->query($consulta_linha);
    
    if ($lista_linha && $lista_linha->num_rows == 1) {
        $linha_atual = $lista_linha->fetch_assoc();
        mysqli_free_result($lista_linha);
    } else {
        $mensagem = "Erro: Linha não encontrada.";
        $tipo_alerta = 'danger';
        $id_linha_filtro = null; 
    }
}


// Se não houver ID válido, redireciona para a lista
if (!$id_linha_filtro && !$mensagem) {
    header("Location: linhas.php?msg_erro=" . urlencode("ID da linha não fornecido."));
    exit();
}

$conn->close();

$titulo_pagina = "Editar Linha";
include '../admin/header.php'; 
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <h2 class="text-info mb-4">
                <i class="bi bi-pencil-fill"></i> Editar Linha de Transporte
            </h2>
            
            <?php if ($mensagem): ?>
                <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
                    <?php echo $mensagem; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($linha_atual): ?>
            <div class="card p-4 shadow">
                <form 
                    action="editar_linha.php"
                    method="post"
                    id="form_linha_atualiza"
                    name="form_linha_atualiza"
                >
                    <input type="hidden" name="id_linha" value="<?php echo $linha_atual['id_linha']; ?>">
                    
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
                                value="<?php echo htmlspecialchars($linha_atual['codigo']); ?>"
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
                                value="<?php echo htmlspecialchars($linha_atual['nome']); ?>"
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
                                value="<?php echo htmlspecialchars($linha_atual['operadora']); ?>"
                            >
                        </div> 
                    </div> 

                    <div class="d-flex justify-content-between">
                         <button 
                            type="submit" 
                            name="enviar"
                            id="enviar"
                            class="btn btn-info text-white"
                         >
                            <i class="bi bi-arrow-repeat"></i> Atualizar Linha
                         </button>
                         <a href="linhas.php" class="btn btn-secondary">
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