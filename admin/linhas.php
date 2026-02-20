<?php
// OBRIGATÓRIO: Proteção de Login e início da sessão
include 'verificar_login.php'; 

// Incluir a conexão
include("../connections/db_connect.php");

// Variável para o nome do seu banco de dados (ajuste se necessário)
$database_conn = "TransportePublico_ti19";

// Selecionar o banco de dados (USE)
mysqli_select_db($conn, $database_conn);

// Selecionar os dados
$consulta   =   "
                SELECT  id_linha, codigo, nome, operadora
                FROM    tblinhas
                ORDER BY nome ASC;
                ";
// Fazer uma lista completa dos dados
$lista      =   $conn->query($consulta);
// Separar os dados em linhas (row)
$row        =   $lista->fetch_assoc();
// Contar o total de linhas
$totalRows  =   ($lista)->num_rows;

$conn->close();

$titulo_pagina = "Linhas de Transporte - Lista";
// O include abaixo foi ajustado para usar o caminho CORRETO (../header.php)
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
    
    <h2 class="text-primary mb-4">
        <i class="bi bi-road-set"></i> Gerenciamento de Linhas de Transporte
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
            Total de Linhas Cadastradas: 
            <span class="badge bg-primary rounded-pill"><?php echo $totalRows; ?></span>
        </div>
        <a href="adicionar_linha.php" class="btn btn-success">
            <i class="bi bi-plus-circle-fill"></i> Adicionar Nova Linha
        </a>
    </div>

    <?php 
    // Verifica se há linhas retornadas
    if ($totalRows > 0) {
    ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover caption-top align-middle">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Código</th>
                        <th scope="col">Nome da Linha</th>
                        <th scope="col">Operadora</th>
                        <th scope="col">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Loop para preencher a tabela com os dados
                    do { 
                    ?>
                        <tr>
                            <th scope="row"><?php echo $row['id_linha']; ?></th>
                            <td><span class="badge bg-secondary"><?php echo $row['codigo']; ?></span></td>
                            <td><?php echo $row['nome']; ?></td>
                            <td><?php echo $row['operadora']; ?></td>
                            <td>
                                <a href="editar_linha.php?id=<?php echo $row['id_linha']; ?>" class="btn btn-sm btn-info text-white me-2" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $row['id_linha'];?>" data-nome="<?php echo $row['nome'];?>" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" title="Excluir">
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
        // Mensagem de alerta do Bootstrap se não houver linhas
        echo '<div class="alert alert-warning" role="alert"><i class="bi bi-exclamation-triangle-fill"></i> Nenhuma linha de transporte cadastrada no sistema.</div>';
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
        <p>Você tem certeza que deseja excluir a linha:</p>
        <h4 class="text-danger fw-bold"><span id="deleteNome"></span></h4>
        <p class="text-muted">Se houver veículos ou rotas associadas, a exclusão será bloqueada.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a id="deleteConfirmLink" href="#" class="btn btn-danger">Sim, Excluir</a>
      </div>
    </div>
  </div>
</div>

<script>
    // Script JS para preencher o modal com os dados da linha
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const deleteNomeSpan = document.getElementById('deleteNome');
        const deleteConfirmLink = document.getElementById('deleteConfirmLink');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nome = this.getAttribute('data-nome');
                
                deleteNomeSpan.textContent = nome;
                deleteConfirmLink.href = `excluir_linha.php?id=${id}`;
            });
        });
    });
</script>

<?php 
// O include abaixo foi ajustado para usar o caminho CORRETO (../footer.php)
include '../admin/footer.php'; 
?>
</body>
