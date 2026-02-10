<?php
include 'verificar_login.php'; 
include("../connections/db_connect.php");

$id_horario = $_GET['id_horario'];
$id_linha   = $_GET['id_linha'];

mysqli_select_db($conn, "TransportePublico_ti19");


//  ADICIONAR PONTO (INSERT)

if (isset($_POST['add_ponto'])) {
    $id_ponto_escolhido = $_POST['id_ponto'];
    $hora_prevista      = $_POST['horario_previsto'];
    $tipo_nesta_rota    = $_POST['tipo_ponto'];
    
    // Pega última ordem
    $ordem = $conn->query("SELECT IFNULL(MAX(ordem),0)+1 as n FROM tbrotas WHERE id_horario = '$id_horario'")->fetch_assoc()['n'];
    
    $sql = "INSERT INTO tbrotas (id_horario, id_ponto, ordem, horario_previsto, tipo_ponto) 
            VALUES ('$id_horario', '$id_ponto_escolhido', '$ordem', '$hora_prevista', '$tipo_nesta_rota')";
    
    if($conn->query($sql)) {
        header("Location: rotas_pontos.php?id_horario=$id_horario&id_linha=$id_linha&msg=adicionado");
        exit();
    }
}


//  EDITAR PONTO (UPDATE)

if (isset($_POST['editar_rota'])) {
    $id_rota_edit = $_POST['id_rota'];
    $novo_horario = $_POST['horario_previsto'];
    $novo_tipo    = $_POST['tipo_ponto'];

    $sql_update = "UPDATE tbrotas SET 
                   horario_previsto = '$novo_horario', 
                   tipo_ponto = '$novo_tipo' 
                   WHERE id_rota = '$id_rota_edit'";

    if($conn->query($sql_update)) {
        header("Location: rotas_pontos.php?id_horario=$id_horario&id_linha=$id_linha&msg=editado");
        exit();
    }
}


// REMOVER PONTO (DELETE)

if (isset($_GET['del_rota'])) {
    $id_rota = $_GET['del_rota'];
    $conn->query("DELETE FROM tbrotas WHERE id_rota = '$id_rota'");
    header("Location: rotas_pontos.php?id_horario=$id_horario&id_linha=$id_linha&msg=removido");
    exit();
}

// BUSCA DADOS
$info_horario = $conn->query("SELECT * FROM tbhorario_programados WHERE id_horario = '$id_horario'")->fetch_assoc();

// Busca pontos da rota
$pontos_rota = $conn->query("
    SELECT R.*, P.nome, R.tipo_ponto as tipo_rota 
    FROM tbrotas R 
    JOIN tbpontos P ON R.id_ponto = P.id_ponto 
    WHERE R.id_horario = '$id_horario' 
    ORDER BY R.ordem ASC
");

// Busca todos os pontos físicos para o select
$todos_pontos = $conn->query("SELECT * FROM tbpontos ORDER BY nome");

include '../admin/header.php'; 
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-muted mb-0">Itinerário da Saída:</h4>
            <h2 class="text-primary fw-bold">
                <i class="bi bi-clock"></i> <?php echo substr($info_horario['horario_partida'], 0, 5); ?>
            </h2>
        </div>
        <a href="rotas_gerenciar.php?id_linha=<?php echo $id_linha; ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Ação realizada com sucesso!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="bi bi-plus-circle"></i> Adicionar Parada
                </div>
                <div class="card-body">
                    <form method="post">
                        <input type="hidden" name="add_ponto" value="1">
                        
                        <div class="mb-3">
                            <label class="form-label">Ponto Físico</label>
                            <select name="id_ponto" class="form-select select2" required>
                                <option value="">-- Selecione --</option>
                                <?php 
                                // Resetar o ponteiro do banco para garantir que a lista apareça completa
                                $todos_pontos->data_seek(0); 
                                while($p = $todos_pontos->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $p['id_ponto']; ?>"><?php echo $p['nome']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Horário Previsto</label>
                            <input type="time" name="horario_previsto" class="form-control" required value="<?php echo $info_horario['horario_partida']; ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tipo nesta viagem</label>
                            <select name="tipo_ponto" class="form-select">
                                <option value="meio" selected>Parada Comum (Meio)</option>
                                <option value="inicio">Ponto Inicial</option>
                                <option value="fim">Ponto Final</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100 fw-bold">
                            <i class="bi bi-save"></i> Adicionar à Rota
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white fw-bold">
                    Itinerário Atual
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Ordem</th>
                                    <th>Ponto</th>
                                    <th>Horário</th>
                                    <th>Tipo</th>
                                    <th class="text-end pe-3">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($pontos_rota->num_rows > 0): ?>
                                    <?php while($rota = $pontos_rota->fetch_assoc()): ?>
                                        <tr>
                                            <td class="ps-3"><span class="badge bg-secondary rounded-pill"><?php echo $rota['ordem']; ?>º</span></td>
                                            <td class="fw-bold"><?php echo $rota['nome']; ?></td>
                                            <td><?php echo substr($rota['horario_previsto'], 0, 5); ?></td>
                                            <td>
                                                <?php 
                                                if($rota['tipo_rota'] == 'inicio') echo '<span class="badge bg-success">Início</span>';
                                                elseif($rota['tipo_rota'] == 'fim') echo '<span class="badge bg-danger">Fim</span>';
                                                else echo '<span class="badge bg-info text-dark">Meio</span>';
                                                ?>
                                            </td>
                                            <td class="text-end pe-3">
                                                <button class="btn btn-primary btn-sm rounded-1 btn-editar"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalEditar"
                                                        data-id="<?php echo $rota['id_rota']; ?>"
                                                        data-nome="<?php echo $rota['nome']; ?>"
                                                        data-horario="<?php echo $rota['horario_previsto']; ?>"
                                                        data-tipo="<?php echo $rota['tipo_rota']; ?>">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                                
                                                <button class="btn btn-danger btn-sm rounded-1 ms-1"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalExcluir"
                                                        data-nome="<?php echo $rota['nome']; ?>"
                                                        data-url="?id_horario=<?php echo $id_horario; ?>&id_linha=<?php echo $id_linha; ?>&del_rota=<?php echo $rota['id_rota']; ?>">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            Nenhum ponto adicionado a esta rota ainda.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExcluir" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title fw-bold">Confirmar Exclusão</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body py-4">
        <p class="mb-0 fs-5">
            Deseja excluir: <strong id="deleteNome" class="text-dark">...</strong> desta rota?
        </p>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
        <a href="#" id="btnConfirmarExclusao" class="btn btn-danger px-4">Excluir</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square"></i> Editar Ponto da Rota</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="post">
          <div class="modal-body py-4">
            <input type="hidden" name="editar_rota" value="1">
            <input type="hidden" name="id_rota" id="edit_id_rota">

            <div class="mb-3">
                <label class="form-label fw-bold">Ponto:</label>
                <input type="text" class="form-control" id="edit_nome_ponto" disabled readonly>
            </div>

            <div class="row">
                <div class="col-6">
                    <label class="form-label">Horário Previsto</label>
                    <input type="time" name="horario_previsto" id="edit_horario" class="form-control" required>
                </div>
                <div class="col-6">
                    <label class="form-label">Tipo</label>
                    <select name="tipo_ponto" id="edit_tipo" class="form-select">
                        <option value="inicio">Início</option>
                        <option value="meio">Meio</option>
                        <option value="fim">Fim</option>
                    </select>
                </div>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary px-4">Salvar Alterações</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // Configuração do Modal de Exclusão
        var modalExcluir = document.getElementById('modalExcluir');
        modalExcluir.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var nomePonto = button.getAttribute('data-nome');
            var urlDeletar = button.getAttribute('data-url');
            
            modalExcluir.querySelector('#deleteNome').textContent = nomePonto;
            modalExcluir.querySelector('#btnConfirmarExclusao').setAttribute('href', urlDeletar);
        });

        // Configuração do Modal de Edição
        var modalEditar = document.getElementById('modalEditar');
        modalEditar.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            
            // Pega os dados do botão
            var idRota = button.getAttribute('data-id');
            var nome = button.getAttribute('data-nome');
            var horario = button.getAttribute('data-horario');
            var tipo = button.getAttribute('data-tipo');
            
            // Preenche o formulário
            document.getElementById('edit_id_rota').value = idRota;
            document.getElementById('edit_nome_ponto').value = nome;
            document.getElementById('edit_horario').value = horario; // Formato HH:MM:SS
            document.getElementById('edit_tipo').value = tipo;
        });
    });
</script>

<?php include '../admin/footer.php'; ?>