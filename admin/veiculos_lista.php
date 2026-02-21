<?php
// Incluir o arquivo de verificação de login para proteger a página
include '../admin/verificar_login.php';

// Incluir a conexão
include("../connections/db_connect.php");

// Variável para o nome do seu banco de dados (ajuste se necessário)
$database_conn = "TransportePublico_ti19";

// Selecionar o banco de dados (USE)
mysqli_select_db($conn, $database_conn);

// Selecionar os dados com JOIN para incluir o nome da linha
$consulta   =   "
                SELECT  
                    v.id_veiculo,
                    v.placa,
                    v.capacidade,
                    v.id_linha,
                    L.codigo AS codigo_linha,
                    L.nome AS nome_linha
                FROM    
                    tbveiculos v
                INNER JOIN 
                    tblinhas L ON v.id_linha = L.id_linha
                ORDER BY 
                    v.placa ASC;
                ";
// Fazer uma lista completa dos dados
$lista      =   $conn->query($consulta);
// Separar os dados em linhas (row)
$row        =   $lista->fetch_assoc();
// Contar o total de linhas
$totalRows  =   ($lista)->num_rows;

$conn->close();

$titulo_pagina = "Veículos - Lista";
include 'header.php'; // Inclui a estrutura Bootstrap 5 e Navbar
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
            <i class="bi bi-bus-front-fill"></i> Gerenciamento de Veículos
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
                Total de Veículos: 
                <span class="badge bg-primary rounded-pill"><?php echo $totalRows; ?></span>
            </div>
            <a href="veiculos_insere.php" class="btn btn-success">
                <i class="bi bi-plus-circle-fill"></i> Adicionar Novo Veículo
            </a>
        </div>

        <?php 
        // Verifica se há veículos retornados
        if ($totalRows > 0) {
        ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover caption-top">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Placa</th>
                            <th scope="col">Capacidade</th>
                            <th scope="col">Linha Associada</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Loop para preencher a tabela com os dados
                        do { 
                        ?>
                            <tr>
                                <th scope="row"><?php echo $row['id_veiculo']; ?></th>
                                <td><?php echo $row['placa']; ?></td>
                                <td><?php echo $row['capacidade']; ?></td>
                                <td>
                                    <span class="badge bg-secondary me-2"><?php echo $row['codigo_linha']; ?></span>
                                    <?php echo $row['nome_linha']; ?>
                                </td>
                                <td>
                                    <a href="veiculos_atualiza.php?id=<?php echo $row['id_veiculo']; ?>" class="btn btn-sm btn-warning text-white me-2" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $row['id_veiculo'];?>" data-placa="<?php echo $row['placa'];?>" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" title="Excluir">
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
            echo '<div class="alert alert-warning" role="alert"><i class="bi bi-exclamation-triangle-fill"></i> Nenhum veículo cadastrado no sistema.</div>';
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
            <p>Você tem certeza que deseja excluir o veículo de placa:</p>
            <h4 class="text-danger fw-bold"><span id="deletePlaca"></span></h4>
            <p class="text-muted">Esta ação é irreversível.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <a id="deleteConfirmLink" href="#" class="btn btn-danger">Sim, Excluir</a>
        </div>
        </div>
    </div>
    </div>

    <script>
        // Script JS para preencher o modal com os dados do veículo
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.delete-btn');
            const deletePlacaSpan = document.getElementById('deletePlaca');
            const deleteConfirmLink = document.getElementById('deleteConfirmLink');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const placa = this.getAttribute('data-placa');
                    
                    deletePlacaSpan.textContent = placa;
                    deleteConfirmLink.href = `veiculos_exclui.php?id=${id}`;
                });
            });
        });
    </script>

    <?php 
    include '../admin/footer.php'; 
    ?>
    <script src="../js/bootstrap.bundle.min.js"></script>
</body> 
<?php
