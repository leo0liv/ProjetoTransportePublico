<?php
include 'verificar_login.php';
include("../connections/db_connect.php");

$database_conn = "TransportePublico_ti19";
mysqli_select_db($conn, $database_conn);

$id_ponto = $_GET['id'];
$mensagem = '';

// LÓGICA DE ATUALIZAÇÃO
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome      = $conn->real_escape_string($_POST['nome']);
    $latitude  = floatval($_POST['latitude']);
    $longitude = floatval($_POST['longitude']);
    $id_atualiza = $_POST['id_ponto'];

    // Update sem o tipo_ponto
    $sql = "UPDATE tbpontos SET nome = '$nome', latitude = '$latitude', longitude = '$longitude' WHERE id_ponto = '$id_atualiza'";
    
    if ($conn->query($sql)) {
        header("Location: pontos_lista.php?msg=" . urlencode("Ponto atualizado com sucesso!"));
        exit();
    } else {
        $mensagem = "Erro ao atualizar: " . $conn->error;
    }
}

// BUSCAR DADOS ATUAIS
$sql_busca = "SELECT * FROM tbpontos WHERE id_ponto = '$id_ponto'";
$dados = $conn->query($sql_busca)->fetch_assoc();

include '../admin/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <h2 class="text-primary mb-4"><i class="bi bi-pencil-square"></i> Editar Ponto</h2>
            
            <?php if ($mensagem) echo "<div class='alert alert-danger'>$mensagem</div>"; ?>

            <div class="card p-4 shadow">
                <form action="pontos_atualiza.php?id=<?php echo $id_ponto; ?>" method="post">
                    <input type="hidden" name="id_ponto" value="<?php echo $dados['id_ponto']; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Nome do Ponto</label>
                        <input type="text" name="nome" class="form-control" value="<?php echo $dados['nome']; ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="number" name="latitude" class="form-control" step="any" value="<?php echo $dados['latitude']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="number" name="longitude" class="form-control" step="any" value="<?php echo $dados['longitude']; ?>" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-3">
                         <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Salvar Alterações</button>
                         <a href="pontos_lista.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../admin/footer.php'; ?>