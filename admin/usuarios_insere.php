<?php
// usuarios_insere.php

// PROTEÇÃO: Somente Administradores podem acessar esta página
include 'verificar_admin.php'; 

// Conexão com Banco
include("../connections/db_connect.php");

//$database_conn = "TransportePublico_ti19";
$mensagem      = '';
$tipo_alerta   = '';

// Processar o formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    mysqli_select_db($conn, $database_conn);

    // Limpeza dos dados
    $nome  = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $senha = $conn->real_escape_string($_POST['senha']);
    
    // PEGANDO O NÍVEL DO USUÁRIO DO FORMULÁRIO
    $nivel = $conn->real_escape_string($_POST['nivel_usuario']);

    // Verifica se o E-mail já existe
    $sql_check = "SELECT id_usuario FROM tbusuarios WHERE email = '$email'";
    $check_result = $conn->query($sql_check);

    if ($check_result->num_rows > 0) {
        $mensagem = "Erro: O e-mail <strong>$email</strong> já está cadastrado.";
        $tipo_alerta = "warning";
    } else {
        // INSERINDO NO BANCO COM A COLUNA nivel_usuario
        $sql_insert = "INSERT INTO tbusuarios (nome, email, senha, nivel_usuario) VALUES (?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql_insert)) {
            // "ssss" indica que são 4 strings (nome, email, senha, nivel)
            $stmt->bind_param("ssss", $nome, $email, $senha, $nivel);
            
            if ($stmt->execute()) {
                $mensagem = "Usuário <strong>$nome</strong> cadastrado com sucesso!";
                $tipo_alerta = "success";
                // Limpar campos
                $nome = ""; $email = "";
            } else {
                $mensagem = "Erro ao cadastrar: " . $stmt->error;
                $tipo_alerta = "danger";
            }
            $stmt->close();
        }
    }
}

$conn->close();

$titulo_pagina = "Adicionar Usuário";
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
            
            <h2 class="text-success mb-4">
                <i class="bi bi-person-plus-fill"></i> Novo Usuário
            </h2>
            
            <?php if ($mensagem): ?>
                <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
                    <?php echo $mensagem; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card p-4 shadow">
                <form action="usuarios_insere.php" method="post" autocomplete="off">
                    
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="nome" class="form-control" required value="<?php echo isset($nome) ? $nome : ''; ?>">
                        </div> 
                    </div> 

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control" required value="<?php echo isset($email) ? $email : ''; ?>">
                        </div> 
                    </div> 

                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="password" name="senha" class="form-control" required>
                        </div> 
                    </div> 

                    <div class="mb-3">
                        <label for="nivel_usuario" class="form-label">Nível de Permissão:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                            <select name="nivel_usuario" id="nivel_usuario" class="form-select" required>
                                <option value="comum">Comum (Apenas Visualiza/Edita Outras Áreas)</option>
                                <option value="admin">Administrador (Pode Gerenciar Usuários)</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between pt-3">
                         <button type="submit" class="btn btn-success text-white">
                            <i class="bi bi-save"></i> Salvar Usuário
                         </button>
                         <a href="usuarios_lista.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar para Lista
                         </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../admin/footer.php'; ?>
</body>