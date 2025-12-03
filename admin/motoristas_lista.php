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
                SELECT  id_motorista, nome, cpf, foto_url, data_nascimento, telefone
                FROM    tbmotoristas
                ORDER BY nome ASC;
                ";
// Fazer uma lista completa dos dados
$lista      =   $conn->query($consulta);
// Separar os dados em linhas (row)
$row        =   $lista->fetch_assoc();
// Contar o total de linhas
$totalRows  =   ($lista)->num_rows;

$conn->close();

$titulo_pagina = "Motoristas - Lista";
include '../admin/header.php'; // Inclui a estrutura Bootstrap 5 e Navbar
?>

<div class="container mt-5">
    
    <h2 class="text-primary mb-4">
        <i class="bi bi-person-badge-fill"></i> Gerenciamento de Motoristas
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
            Total de Motoristas Cadastrados: 
            <span class="badge bg-primary rounded-pill"><?php echo $totalRows; ?></span>
        </div>
        <a href="motoristas_insere.php" class="btn btn-success">
            <i class="bi bi-person-plus-fill"></i> Adicionar Novo Motorista
        </a>
    </div>

    <?php 
    // Verifica se há motoristas retornados
    if ($totalRows > 0) {
    ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover caption-top align-middle">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Foto</th>
                        <th scope="col">Nome</th>
                        <th scope="col">CPF</th>
                        <th scope="col">Nascimento</th>
                        <th scope="col">Telefone</th>
                        <th scope="col">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Loop para preencher a tabela com os dados
                    do { 
                        // Formata a data para o padrão brasileiro
                        $data_nasc_formatada = date('d/m/Y', strtotime($row['data_nascimento']));
                    ?>
                        <tr>
                            <td>
                                <?php if ($row['foto_url']): ?>
                                    <img 
                                        src="../imagens/motoristas/<?php echo $row['foto_url']; ?>" 
                                        alt="Foto de <?php echo $row['nome']; ?>"
                                        class="img-thumbnail"
                                        style="width: 50px; height: 50px; object-fit: cover;"
                                    >
                                <?php else: ?>
                                    <i class="bi bi-person-circle text-secondary" style="font-size: 2rem;"></i>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $row['nome']; ?></td>
                            <td><?php echo $row['cpf']; ?></td>
                            <td><?php echo $data_nasc_formatada; ?></td>
                            <td><?php echo $row['telefone']; ?></td>
                            <td>
                                <a href="motoristas_atualiza.php?id=<?php echo $row['id_motorista']; ?>" class="btn btn-sm btn-info text-white me-2" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $row['id_motorista'];?>" data-nome="<?php echo $row['nome'];?>" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" title="Excluir">
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
        // Mensagem de alerta do Bootstrap se não houver motoristas
        echo '<div class="alert alert-warning" role="alert"><i class="bi bi-exclamation-triangle-fill"></i> Nenhum motorista cadastrado no sistema.</div>';
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
        <p>Você tem certeza que deseja excluir o motorista:</p>
        <h4 class="text-danger fw-bold"><span id="deleteNome"></span></h4>
        <p class="text-muted">Se este motorista estiver alocado em algum veículo (`tbmotoristas_alocados`), a exclusão será bloqueada.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a id="deleteConfirmLink" href="#" class="btn btn-danger">Sim, Excluir</a>
      </div>
    </div>
  </div>
</div>

<script>
    // Script JS para preencher o modal com os dados do motorista
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const deleteNomeSpan = document.getElementById('deleteNome');
        const deleteConfirmLink = document.getElementById('deleteConfirmLink');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nome = this.getAttribute('data-nome');
                
                deleteNomeSpan.textContent = nome;
                deleteConfirmLink.href = `motoristas_exclui.php?id=${id}`;
            });
        });
    });
</script>

<?php 
include '../admin/footer.php'; // Inclui o script JS do Bootstrap
?>