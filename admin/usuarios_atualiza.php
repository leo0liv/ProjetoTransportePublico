<?php
// usuarios_editar.php
include 'verificar_admin.php'; // PROTEÇÃO DE ADMIN AQUI
include("../connections/db_connect.php");

//$database_conn = "TransportePublico_ti19";
mysqli_select_db($conn, $database_conn);

$mensagem = ''; $tipo_alerta = '';
$usuario_atual = null;

// Processar a atualização
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_usuario'])) {
    $id_usuario = $conn->real_escape_string($_POST['id_usuario']);
    $nome  = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $senha = $conn->real_escape_string($_POST['senha']);
    $nivel = $conn->real_escape_string($_POST['nivel_usuario']);

    $updateSQL = "UPDATE tbusuarios SET nome = '$nome', email = '$email', senha = '$senha', nivel_usuario = '$nivel' WHERE id_usuario = '$id_usuario'";
    
    if ($conn->query($updateSQL)) {
        header("Location: usuarios_lista.php?msg=" . urlencode("Usuário atualizado com sucesso!"));
        exit();
    } else {
        $mensagem = "Erro ao atualizar: " . $conn->error;
        $tipo_alerta = 'danger';
    }
}

// Buscar dados atuais para preencher o formulário
if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $result = $conn->query("SELECT * FROM tbusuarios WHERE id_usuario = '$id'");
    if ($result->num_rows == 1) {
        $usuario_atual = $result->fetch_assoc();
    } else {
        header("Location: usuarios_lista.php?msg_erro=" . urlencode("Usuário não encontrado."));
        exit();
    }
}

$titulo_pagina = "Editar Usuário";
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
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-warning mb-4"><i class="bi bi-person-lines-fill"></i> Editar Usuário</h2>
            
            <?php if ($mensagem): ?>
                <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show"><?php echo $mensagem; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <?php if ($usuario_atual): ?>
            <div class="card p-4 shadow">
                <form action="usuarios_atualiza.php" method="post">
                    <input type="hidden" name="id_usuario" value="<?php echo $usuario_atual['id_usuario']; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Nome:</label>
                        <input type="text" name="nome" class="form-control" required value="<?php echo htmlspecialchars($usuario_atual['nome']); ?>">
                    </div> 

                    <div class="mb-3">
                        <label class="form-label">E-mail:</label>
                        <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($usuario_atual['email']); ?>">
                    </div> 

                    <div class="mb-3">
                        <label class="form-label">Senha:</label>
                        <input type="text" name="senha" class="form-control" required value="<?php echo htmlspecialchars($usuario_atual['senha']); ?>">
                    </div> 

                    <div class="mb-3">
                        <label class="form-label">Nível de Permissão:</label>
                        <select name="nivel_usuario" class="form-select" required>
                            <option value="comum" <?php if($usuario_atual['nivel_usuario'] == 'comum') echo 'selected'; ?>>Comum (Apenas Visualiza/Edita Outras Áreas)</option>
                            <option value="admin" <?php if($usuario_atual['nivel_usuario'] == 'admin') echo 'selected'; ?>>Administrador (Pode Gerenciar Usuários)</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                         <button type="submit" class="btn btn-warning text-white"><i class="bi bi-save"></i> Atualizar Dados</button>
                         <a href="usuarios_lista.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Voltar</a>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $conn->close(); include '../admin/footer.php'; ?>
</body>