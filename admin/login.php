<?php
// 1. Incluir o arquivo de conexão
require_once("../connections/db_connect.php");

// 2. Configura a sessão
session_name("transporte_publico");
session_start();

// 3. Processa o login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    
    $email_login = $_POST['email'];
    $senha_login = $_POST['senha'];

    // Verifica conexão
    if (!isset($conn)) {
        die("Erro: Conexão com banco de dados não encontrada.");
    }

    // 4. CONSULTA SIMPLES (Sem Hash)
    $sql = "SELECT * FROM tbusuarios WHERE email = ? AND senha = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $email_login, $senha_login);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $linha = $resultado->fetch_assoc();

        if ($resultado->num_rows > 0) {
            // Sucesso! Define as variáveis de sessão
            $_SESSION['login_usuario'] = $linha['email'];
            $_SESSION['nome_usuario']  = $linha['nome'];
            $_SESSION['nivel_usuario'] = 'sup'; 
            $_SESSION['nome_da_sessao'] = session_name();
            
            // --- MUDANÇA AQUI: Redireciona para a Área Administrativa ---
            header("Location: adm_options.php");
            exit;
        } else {
            // Erro
            header("Location: invasor.php");
            exit;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrativo</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin-top: 100px;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background-color: white;
        }
    </style>
</head>
<body>
 
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 login-container">
           
            <h3 class="text-center text-primary mb-4">
                <i class="bi bi-person-lock"></i> Acesso Administrativo
            </h3>
 

 
            <form action="login.php" method="POST">
               
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
 
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                </div>
 
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary mt-3">
                        <i class="bi bi-box-arrow-in-right"></i> Entrar
                    </button>
                </div>
 
            </form>
 
        </div>
    </div>
</div>
 
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>