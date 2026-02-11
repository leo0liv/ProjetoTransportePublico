<?php
include 'verificar_login.php'; 
include("../connections/db_connect.php");

// Verifica se o ID da linha foi passado
if (!isset($_GET['id_linha'])) {
    header("Location: rotas_lista.php");
    exit();
}

$id_linha = $conn->real_escape_string($_GET['id_linha']);
$database_conn = "TransportePublico_ti19";
mysqli_select_db($conn, $database_conn);


//  CRIAR NOVO HORÁRIO (INSERT)

if (isset($_POST['criar_horario'])) {
    $dia = $_POST['dia_semana'];
    $hora = $_POST['horario_partida'];
    
    $sql = "INSERT INTO tbhorario_programados (id_linha, dia_semana, horario_partida) VALUES ('$id_linha', '$dia', '$hora')";
    if($conn->query($sql)) {
        header("Location: rotas_gerenciar.php?id_linha=$id_linha&msg=criado");
        exit();
    }
}


//  EDITAR HORÁRIO (UPDATE)

if (isset($_POST['editar_horario'])) {
    $id_edit = $_POST['id_horario'];
    $dia_edit = $_POST['dia_semana'];
    $hora_edit = $_POST['horario_partida'];
    
    $sql = "UPDATE tbhorario_programados SET dia_semana = '$dia_edit', horario_partida = '$hora_edit' WHERE id_horario = '$id_edit'";
    
    if($conn->query($sql)) {
        header("Location: rotas_gerenciar.php?id_linha=$id_linha&msg=editado");
        exit();
    }
}


//  EXCLUIR HORÁRIO (DELETE)

if (isset($_GET['del'])) {
    $id_del = $conn->real_escape_string($_GET['del']);
    $conn->query("DELETE FROM tbhorario_programados WHERE id_horario = '$id_del'");
    header("Location: rotas_gerenciar.php?id_linha=$id_linha&msg=removido");
    exit();
}

// BUSCAR DADOS
$linha = $conn->query("SELECT * FROM tblinhas WHERE id_linha = '$id_linha'")->fetch_assoc();
$horarios = $conn->query("SELECT * FROM tbhorario_programados WHERE id_linha = '$id_linha' ORDER BY dia_semana, horario_partida");

include '../admin/header.php'; 
?>

<div class="container mt-5">
    
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Ação realizada com sucesso!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="text-primary fw-bold mb-0">Horários da Linha: <?php echo $linha['codigo']; ?></h2>
            <p class="text-muted fs-5 mb-0"><?php echo $linha['nome']; ?></p>
        </div>
        <div>
            <a href="rotas_lista.php" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            <button class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNovoHorario">
                <i class="bi bi-plus-lg"></i> Novo Horário de Saída
            </button>
        </div>
    </div>

    <div class="row g-4">
        <?php while($h = $horarios->fetch_assoc()): ?>
            <div class="col-md-4">
                <div class="card shadow-sm h-100 border-start border-4 border-primary hover-card">
                    <div class="card-body">
                        
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-clock fs-3 me-2 text-primary"></i>
                            <h3 class="card-title fw-bold text-dark m-0">
                                <?php echo substr($h['horario_partida'], 0, 5); ?>
                            </h3>
                        </div>
                        <span class="badge bg-secondary mb-3"><?php echo ucfirst($h['dia_semana']); ?></span>
                        
                        <p class="card-text text-muted small mb-4">
                            Clique em "Gerenciar Pontos" para definir o itinerário deste horário.
                        </p>
                        
                        <div class="d-flex gap-2">
                            <a href="rotas_pontos.php?id_horario=<?php echo $h['id_horario']; ?>&id_linha=<?php echo $id_linha; ?>" class="btn btn-outline-primary w-100 fw-bold">
                                <i class="bi bi-map"></i> Gerenciar Pontos
                            </a>

                            <button type="button" 
                                    class="btn btn-info text-white" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEditarHorario"
                                    data-id="<?php echo $h['id_horario']; ?>"
                                    data-dia="<?php echo $h['dia_semana']; ?>"
                                    data-hora="<?php echo $h['horario_partida']; ?>"
                                    title="Editar">
                                <i class="bi bi-pencil-fill"></i>
                            </button>

                            <button type="button"
                                    class="btn btn-outline-danger" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalExcluirHorario"
                                    data-id="<?php echo $h['id_horario']; ?>"
                                    data-texto="<?php echo substr($h['horario_partida'], 0, 5) . ' (' . ucfirst($h['dia_semana']) . ')'; ?>"
                                    title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        
        <?php if($horarios->num_rows == 0): ?>
            <div class="col-12">
                <div class="alert alert-warning text-center py-5">
                    <i class="bi bi-clock-history display-1 d-block mb-3 text-warning"></i>
                    <h4 class="alert-heading">Nenhum horário cadastrado!</h4>
                    <p>Clique no botão "Novo Horário de Saída" para começar.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="modalNovoHorario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle"></i> Novo Horário</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body py-4">
                    <input type="hidden" name="criar_horario" value="1">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dia da Semana</label>
                        <select name="dia_semana" class="form-select" required>
                            <option value="Segunda-Sexta">Segunda a Sexta</option>
                            <option value="Sabado">Sábado</option>
                            <option value="Domingo/Feriado">Domingo/Feriado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Horário de Partida</label>
                        <input type="time" name="horario_partida" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarHorario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square"></i> Editar Horário</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body py-4">
                    <input type="hidden" name="editar_horario" value="1">
                    <input type="hidden" name="id_horario" id="edit_id_horario">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dia da Semana</label>
                        <select name="dia_semana" id="edit_dia_semana" class="form-select" required>
                            <option value="Segunda-Sexta">Segunda a Sexta</option>
                            <option value="Sabado">Sábado</option>
                            <option value="Domingo">Domingo/Feriado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Horário de Partida</label>
                        <input type="time" name="horario_partida" id="edit_horario_partida" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info text-white fw-bold px-4">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExcluirHorario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">Confirmar Exclusão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4">
                <p class="fs-5 mb-0">
                    Deseja excluir o horário: <strong id="deleteTexto">...</strong>?
                </p>
                <p class="text-danger small mt-2 mb-0">
                    <i class="bi bi-exclamation-triangle"></i> Atenção: Isso também apagará todos os pontos configurados dentro deste horário.
                </p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="linkExcluir" class="btn btn-danger px-4">Excluir</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // Configuração Modal EDITAR
        var modalEditar = document.getElementById('modalEditarHorario');
        modalEditar.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var dia = button.getAttribute('data-dia');
            var hora = button.getAttribute('data-hora');
            
            document.getElementById('edit_id_horario').value = id;
            document.getElementById('edit_dia_semana').value = dia;
            document.getElementById('edit_horario_partida').value = hora;
        });

        // Configuração Modal EXCLUIR
        var modalExcluir = document.getElementById('modalExcluirHorario');
        modalExcluir.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var texto = button.getAttribute('data-texto');
            
            // Atualiza texto e link
            document.getElementById('deleteTexto').textContent = texto;
            document.getElementById('linkExcluir').setAttribute('href', '?id_linha=<?php echo $id_linha; ?>&del=' + id);
        });
    });
</script>

<style>
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        transition: transform 0.2s;
    }
</style>

<?php include '../admin/footer.php'; ?>