<?php
// usuarios_lista.php

// 1. PROTEÇÃO: Apenas administradores podem ver a lista de usuários
include 'verificar_admin.php'; 
include("../connections/db_connect.php");

$database_conn = "TransportePublico_ti19";
mysqli_select_db($conn, $database_conn);

// Selecionar todos os usuários, incluindo o nível
$consulta = "SELECT id_usuario, nome, email, nivel_usuario FROM tbusuarios ORDER BY nome ASC";
$lista    = $conn->query($consulta);
$row      = $lista->fetch_assoc();
$totalRows = $lista->num_rows;

$conn->close();

$titulo_pagina = "Lista de Usuários";
include '../admin/header.php'; 
?>

<div class="container mt-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary">
            <i class="bi bi-people-fill"></i> Gerenciar Usuários
        </h2>
        <a href="usuarios_insere.php" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Adicionar Usuário
        </a>
    </div>

    <?php 
    if (isset($_GET['msg'])) {
        echo '<div class="alert alert-success alert-dismissible fade show">' . htmlspecialchars($_GET['msg']) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
    if (isset($_GET['msg_erro'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show">' . htmlspecialchars($_GET['msg_erro']) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
    ?>

    <?php if ($totalRows > 0) { ?>
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Permissão</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php do { ?>
                            <tr>
                                <td><?php echo $row['id_usuario']; ?></td>
                                <td class="fw-bold"><?php echo $row['nome']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td>
                                    <?php if(isset($row['nivel_usuario']) && $row['nivel_usuario'] == 'admin'): ?>
                                        <span class="badge bg-danger">Administrador</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Comum</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="usuarios_atualiza.php?id=<?php echo $row['id_usuario']; ?>" class="btn btn-sm btn-info text-white me-2" title="Editar Usuário">
                                        <i class="bi bi-pencil-fill"></i> Editar
                                    </a>
                                    
                                    <button class="btn btn-sm btn-danger delete-btn" 
                                            data-id="<?php echo $row['id_usuario']; ?>" 
                                            data-nome="<?php echo $row['nome']; ?>" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#confirmDeleteModal"
                                            title="Excluir Usuário">
                                        <i class="bi bi-trash"></i> Excluir
                                    </button>
                                </td>
                            </tr>
                        <?php } while($row = $lista->fetch_assoc()); ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } else { ?>
        <div class="alert alert-info">Nenhum usuário encontrado.</div>
    <?php } ?>

</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Excluir Usuário</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Tem certeza que deseja remover o usuário:</p>
        <h4 class="text-danger fw-bold text-center" id="deleteNome"></h4>
        <p class="text-muted small text-center">Esta ação não pode ser desfeita.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a id="deleteConfirmLink" href="#" class="btn btn-danger">Confirmar Exclusão</a>
      </div>
    </div>
  </div>
</div>

<script>
    // Script para passar os dados para o modal
    document.addEventListener('DOMContentLoaded', function () {
        var confirmModal = document.getElementById('confirmDeleteModal');
        confirmModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var nome = button.getAttribute('data-nome');
            
            document.getElementById('deleteNome').textContent = nome;
            document.getElementById('deleteConfirmLink').href = 'usuarios_excluir.php?id=' + id;
        });
    });
</script>
<br>
<br>

<?php include '../admin/footer.php'; ?>