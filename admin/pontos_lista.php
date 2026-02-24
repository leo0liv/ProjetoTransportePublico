<?php
// Incluir o arquivo de verificação de login
include 'verificar_login.php';
// Incluir a conexão
include("../connections/db_connect.php");

$database_conn = "TransportePublico_ti19";
mysqli_select_db($conn, $database_conn);

// CONSULTA
$consulta = "SELECT id_ponto, nome, latitude, longitude FROM tbpontos ORDER BY nome ASC";
$lista    = $conn->query($consulta);
$totalRows = ($lista)->num_rows;

$conn->close();
$titulo_pagina = "Pontos de Ônibus - Lista";
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
        <i class="bi bi-geo-alt-fill"></i> Gerenciamento de Pontos Físicos
    </h2>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['msg_erro'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['msg_erro']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="alert alert-info py-2 m-0">
            Total de Pontos: <span class="badge bg-primary rounded-pill"><?php echo $totalRows; ?></span>
        </div>
        <a href="pontos_insere.php" class="btn btn-success">
            <i class="bi bi-plus-circle-fill"></i> Novo Ponto
        </a>
    </div>

    <?php if ($totalRows > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover caption-top shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome do Ponto</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $lista->fetch_assoc()): ?>
                        <tr>
                            <th><?php echo $row['id_ponto']; ?></th>
                            <td><strong><?php echo $row['nome']; ?></strong></td>
                            <td><?php echo $row['latitude']; ?></td>
                            <td><?php echo $row['longitude']; ?></td>
                            <td>
                                <a href="pontos_atualiza.php?id=<?php echo $row['id_ponto']; ?>" class="btn btn-sm btn-warning text-white me-1" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                
                                <button class="btn btn-sm btn-danger delete-btn" 
                                        data-id="<?php echo $row['id_ponto'];?>" 
                                        data-nome="<?php echo $row['nome'];?>" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#confirmDeleteModal" 
                                        title="Excluir">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle-fill"></i> Nenhum ponto cadastrado.
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirmar Exclusão</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Deseja excluir: <strong id="deleteNome"></strong>?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a id="deleteConfirmLink" href="#" class="btn btn-danger">Excluir</a>
      </div>
    </div>
  </div>
</div>

<script>
    var confirmDeleteModal = document.getElementById('confirmDeleteModal');
    confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var nome = button.getAttribute('data-nome');
        document.getElementById('deleteNome').textContent = nome;
        document.getElementById('deleteConfirmLink').setAttribute('href', 'pontos_exclui.php?id=' + id);
    });
</script>

<?php include '../admin/footer.php'; ?>
</body>
