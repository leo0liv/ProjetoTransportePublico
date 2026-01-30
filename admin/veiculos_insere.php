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

    $tabela_insert  =   "tbveiculos";
    $campos_insert  =   "placa, id_linha, capacidade";
    
    // Recebe e sanitiza os dados do formulário
    $placa          =   $conn->real_escape_string(strtoupper($_POST['placa']));
    $id_linha       =   $conn->real_escape_string($_POST['id_linha']);
    $capacidade     =   $conn->real_escape_string($_POST['capacidade']);     

    // Query de Inserção
    $insertSQL  =   "
                    INSERT INTO ".$tabela_insert."
                        (".$campos_insert.")
                    VALUES
                        ('$placa', '$id_linha', '$capacidade');
                    ";
    $resultado  =   $conn->query($insertSQL);

    $destino    =   "veiculos_lista.php";
    if($resultado){
        $mensagem = "Veículo de placa **$placa** cadastrado com sucesso!";
        $tipo_alerta = 'success';
    } else {
        if ($conn->errno == 1062) {
             $mensagem = "Erro: A placa **$placa** já existe no sistema.";
             $tipo_alerta = 'warning';
        } else {
             $mensagem = "Erro ao cadastrar veículo: " . $conn->error;
             $tipo_alerta = 'danger';
        }
    };
};

// Lógica para SELECT na Chave Estrangeira (tblinhas)
mysqli_select_db($conn, $database_conn);

$tabela_fk      =   "tblinhas";
$ordenar_por    =   "nome ASC";
$consulta_fk    =   "
                    SELECT id_linha, codigo, nome
                    FROM    ".$tabela_fk."
                    ORDER BY ".$ordenar_por.";
                    ";
$lista_fk       =   $conn->query($consulta_fk);
$row_fk         =   $lista_fk->fetch_assoc();
$totalRows_fk   =   ($lista_fk)->num_rows;

$conn->close();

$titulo_pagina = "Inserir Veículo";
include '../admin/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <h2 class="text-primary mb-4">
                <i class="bi bi-plus-circle-fill"></i> Cadastrar Novo Veículo
            </h2>
            
            <?php if ($mensagem): ?>
                <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
                    <?php echo $mensagem; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card p-4 shadow"> <form 
                    action="veiculos_insere.php"
                    method="post"
                    id="form_veiculo_insere"
                    name="form_veiculo_insere"
                >
                    
                    <div class="mb-3">
                        <label for="id_linha" class="form-label">Linha Associada:</label>
                        <select 
                            name="id_linha" 
                            id="id_linha"
                            class="form-select"
                            required
                        >
                            <option value="">Selecione a Linha</option>
                            <?php do{ ?>
                                <option value="<?php echo $row_fk['id_linha']; ?>">
                                    <?php echo $row_fk['codigo'] . ' - ' . $row_fk['nome']; ?>
                                </option>
                            <?php }while($row_fk=$lista_fk->fetch_assoc()); ?>
                            </select>
                        <?php if ($totalRows_fk == 0): ?>
                             <div class="alert alert-warning mt-2 p-2">
                                 <i class="bi bi-exclamation-triangle-fill"></i> Nenhuma linha cadastrada. Cadastre uma linha primeiro.
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
                            >
                        </div> 
                    </div> <div class="mb-3">
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
                            >
                        </div> 
                    </div> <div class="d-flex justify-content-between">
                         <button 
                            type="submit" 
                            name="enviar"
                            id="enviar"
                            class="btn btn-primary"
                         >
                            <i class="bi bi-bus-fill"></i> Cadastrar Veículo
                         </button>
                         <a href="veiculos_lista.php" class="btn btn-secondary">
                             <i class="bi bi-arrow-left-circle-fill"></i> Voltar
                         </a>
                    </div>
                </form>
            </div> </div>
    </div>
</div>

<?php 
// Libera o resultado da consulta
if (isset($lista_fk)) {
    mysqli_free_result($lista_fk); 
}
include '../admin/footer.php'; 
?>