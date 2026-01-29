<?php
// OBRIGATÓRIO: Proteção de Login
include 'verificar_login.php'; 

// Incluir a conexão
include("../connections/db_connect.php");

$database_conn = "TransportePublico_ti19";
$mensagem = '';
$tipo_alerta = '';

// Verifica se o ID da linha foi passado
if (!isset($_GET['id_linha']) || !is_numeric($_GET['id_linha'])) {
    header("Location: rotas_lista.php?msg_erro=Linha inválida");
    exit();
}

$id_linha_filtro = $conn->real_escape_string($_GET['id_linha']);
mysqli_select_db($conn, $database_conn);

// --- LÓGICA DE AÇÃO: ADICIONAR PONTO ---
if (isset($_POST['acao']) && $_POST['acao'] == 'adicionar' && isset($_POST['id_ponto'])) {
    $id_ponto = $conn->real_escape_string($_POST['id_ponto']);
    
    // 1. Descobrir a próxima ordem
    $res_ordem = $conn->query("SELECT IFNULL(MAX(ordem), 0) + 1 AS proxima FROM tbrotas WHERE id_linha = '$id_linha_filtro'");
    $proxima_ordem = $res_ordem->fetch_assoc()['proxima'];
    
    // 2. Inserir na rota
    $sql_add = "INSERT INTO tbrotas (id_linha, id_ponto, ordem) VALUES ('$id_linha_filtro', '$id_ponto', '$proxima_ordem')";
    if ($conn->query($sql_add)) {
        $mensagem = "Ponto adicionado à rota!";
        $tipo_alerta = "success";
    } else {
        $mensagem = "Erro ao adicionar: " . $conn->error;
        $tipo_alerta = "danger";
    }
}

// --- LÓGICA DE AÇÃO: REMOVER PONTO ---
if (isset($_GET['remover_ponto']) && is_numeric($_GET['remover_ponto'])) {
    $id_ponto_rem = $conn->real_escape_string($_GET['remover_ponto']);
    
    $sql_del = "DELETE FROM tbrotas WHERE id_linha = '$id_linha_filtro' AND id_ponto = '$id_ponto_rem'";
    if ($conn->query($sql_del)) {
        // Reordenar os pontos restantes para não haver buracos na sequência
        $conn->query("SET @ordem := 0");
        $conn->query("UPDATE tbrotas SET ordem = (@ordem := @ordem + 1) WHERE id_linha = '$id_linha_filtro' ORDER BY ordem ASC");
        
        $mensagem = "Ponto removido e itinerário reordenado.";
        $tipo_alerta = "info";
    }
}

// 1. Buscar informações da Linha
$res_linha = $conn->query("SELECT * FROM tblinhas WHERE id_linha = '$id_linha_filtro'");
$linha_info = $res_linha->fetch_assoc();

// 2. Buscar pontos que JÁ ESTÃO na rota desta linha
$sql_rota = "
    SELECT R.id_ponto, R.ordem, P.nome 
    FROM tbrotas R
    INNER JOIN tbpontos P ON R.id_ponto = P.id_ponto
    WHERE R.id_linha = '$id_linha_filtro'
    ORDER BY R.ordem ASC
";
$lista_rota = $conn->query($sql_rota);

// 3. Buscar pontos DISPONÍVEIS (que ainda não estão nesta linha)
$sql_disponiveis = "
    SELECT id_ponto, nome 
    FROM tbpontos 
    WHERE id_ponto NOT IN (SELECT id_ponto FROM tbrotas WHERE id_linha = '$id_linha_filtro')
    ORDER BY nome ASC
";
$lista_disponiveis = $conn->query($sql_disponiveis);

$titulo_pagina = "Configurar Rota - " . $linha_info['codigo'];
include '../admin/header.php'; 
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary m-0">Itinerário: <?php echo $linha_info['codigo']; ?></h2>
            <p class="text-muted"><?php echo $linha_info['nome']; ?></p>
        </div>
        <a href="rotas_lista.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar para Lista
        </a>
    </div>

    <?php if ($mensagem): ?>
        <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
            <?php echo $mensagem; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white d-flex justify-content-between">
                    <span><i class="bi bi-list-ol"></i> Ordem das Paradas</span>
                    <span class="badge bg-primary"><?php echo $lista_rota->num_rows; ?> Paradas</span>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php if ($lista_rota->num_rows > 0): ?>
                            <?php while($pt = $lista_rota->fetch_assoc()): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <span class="badge bg-info text-dark rounded-pill me-2"><?php echo $pt['ordem']; ?>º</span>
                                        <strong><?php echo $pt['nome']; ?></strong>
                                    </div>
                                    
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-sm"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalExcluir"
                                            data-nome="<?php echo $pt['nome']; ?>"
                                            data-url="rotas_gerenciar.php?id_linha=<?php echo $id_linha_filtro; ?>&remover_ponto=<?php echo $pt['id_ponto']; ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li class="list-group-item text-center py-5 text-muted">
                                <i class="bi bi-geo-alt display-1 d-block mb-3"></i>
                                Nenhuma parada definida para esta linha.
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-plus-circle"></i> Adicionar Parada
                </div>
                <div class="card-body">
                    <form action="rotas_gerenciar.php?id_linha=<?php echo $id_linha_filtro; ?>" method="post">
                        <input type="hidden" name="acao" value="adicionar">
                        
                        <div class="mb-3">
                            <label for="id_ponto" class="form-label">Escolha um ponto cadastrado:</label>
                            <select name="id_ponto" id="id_ponto" class="form-select" required>
                                <option value="">-- Selecione o Ponto --</option>
                                <?php while($disp = $lista_disponiveis->fetch_assoc()): ?>
                                    <option value="<?php echo $disp['id_ponto']; ?>"><?php echo $disp['nome']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-plus-lg"></i> Adicionar ao Itinerário
                        </button>
                    </form>
                    
                    <?php if ($lista_disponiveis->num_rows == 0): ?>
                        <div class="alert alert-warning mt-3 mb-0 small">
                            Não há mais pontos disponíveis para adicionar. 
                            <a href="pontos_insere.php">Cadastrar novos pontos?</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="alert alert-secondary mt-3 small">
                <i class="bi bi-lightbulb"></i> Os pontos são adicionados automaticamente ao <strong>final</strong> da rota. Para remover e alterar a ordem, exclua e adicione novamente na sequência correta.
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExcluir" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmação de Exclusão
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
        <p class="fw-bold mb-2">Você tem certeza que deseja excluir o ponto:</p>
        
        <div class="alert alert-light border text-danger text-center fw-bold" id="nomePontoModal">
            ...
        </div>

        <p class="text-muted small mb-0">
          Se este ponto estiver associado a uma rota, a exclusão será bloqueada.
        </p>
      </div>
      
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a href="#" id="btnConfirmarExclusao" class="btn btn-danger">Sim, Excluir</a>
      </div>
      
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalExcluir = document.getElementById('modalExcluir');
        
        modalExcluir.addEventListener('show.bs.modal', function (event) {
            // Botão que acionou o modal
            var button = event.relatedTarget;
            
            // Extrair info dos atributos data-*
            var nomePonto = button.getAttribute('data-nome');
            var urlDeletar = button.getAttribute('data-url');
            
            // Atualizar o conteúdo do modal
            var modalNome = modalExcluir.querySelector('#nomePontoModal');
            var modalLink = modalExcluir.querySelector('#btnConfirmarExclusao');

            modalNome.textContent = nomePonto;
            modalLink.setAttribute('href', urlDeletar);
        });
    });
</script>

<?php 
$conn->close();
include '../admin/footer.php'; 
?>