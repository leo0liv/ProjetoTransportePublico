<?php
// Incluir o arquivo de verificação de login para proteger a página
include 'verificar_login.php';

// Incluir a conexão
include("../connections/db_connect.php");

// Variável para o nome do seu banco de dados (ajuste se necessário)
$database_conn = "TransportePublico_ti19";

// Selecionar o banco de dados (USE)
mysqli_select_db($conn, $database_conn);

// Selecionar os dados
$consulta   =   "
                SELECT  id_ponto, nome, latitude, longitude
                FROM    tbpontos
                ORDER BY nome ASC;
                ";
// Fazer uma lista completa dos dados
$lista      =   $conn->query($consulta);
// Separar os dados em linhas (row)
$row        =   $lista->fetch_assoc();
// Contar o total de linhas
$totalRows  =   ($lista)->num_rows;

$conn->close();

$titulo_pagina = "Pontos de Ônibus - Lista";
include '../admin/header.php'; // Inclui a estrutura Bootstrap 5 e Navbar
?>

<div class="container mt-5">
    
    <h2 class="text-primary mb-4">
        <i class="bi bi-geo-alt"></i> Gerenciamento de Pontos de Ônibus
    </h2>

    <?php 
    if (isset($_GET['msg'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . htmlspecialchars($_GET['msg']) . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
    if (isset($_GET['msg_erro'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . htmlspecialchars($_GET['msg_erro']) . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
    ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="alert alert-info py-2 m-0">
            Total de Pontos Cadastrados: 
            <span class="badge bg-primary rounded-pill"><?php echo $totalRows; ?></span>
        </div>
        <a href="pontos_insere.php" class="btn btn-success">
            <i class="bi bi-plus-circle-fill"></i> Adicionar Novo Ponto
        </a>
    </div>

    <?php 
    // Verifica se há pontos retornados
    if ($totalRows > 0) {
    ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover caption-top">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nome do Ponto</th>
                        <th scope="col">Latitude</th>
                        <th scope="col">Longitude</th>
                        <th scope="col">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Loop para preencher a tabela com os dados
                    do { 
                    ?>
                        <tr>
                            <th scope="row"><?php echo $row['id_ponto']; ?></th>
                            <td><?php echo $row['nome']; ?></td>
                            <td><?php echo $row['latitude']; ?></td>
                            <td><?php echo $row['longitude']; ?></td>
                            <td>
                                <a href="pontos_atualiza.php?id=<?php echo $row['id_ponto']; ?>" class="btn btn-sm btn-info text-white me-2" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $row['id_ponto'];?>" data-nome="<?php echo $row['nome'];?>" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" title="Excluir">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                    <?php 
                    } while($row = $lista->fetch_assoc()); // Fim do do/while
                    ?>
                </tbody>
            </table>
        </div>
    <?php 
    } else {
        // Mensagem de alerta do Bootstrap se não houver pontos
        echo '<div class="alert alert-warning" role="alert"><i class="bi bi-exclamation-triangle-fill"></i> Nenhum ponto de ônibus cadastrado no sistema.</div>';
    }

    // Libera o resultado da consulta
    if (isset($lista)) {
        mysqli_free_result($lista); 
    }
    ?>
</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeleteModalLabel"><i class="bi bi-exclamation-triangle-fill"></i> Confirmação de Exclusão</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Você tem certeza que deseja excluir o ponto:</p>
        <h4 class="text-danger fw-bold"><span id="deleteNome"></span></h4>
        <p class="text-muted">Se este ponto estiver associado a uma rota, a exclusão será bloqueada.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a id="deleteConfirmLink" href="#" class="btn btn-danger">Sim, Excluir</a>
      </div>
    </div>
  </div>
</div>


<?php 
include '../admin/footer.php'; 
?>   
