<?php
// Incluir o arquivo de conexão
require_once("../connections/db_connect.php");

// Configura a sessão
session_name("transporte_publico");
session_start();

// Processa o login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    
    $email_login = $_POST['email'];
    $senha_login = $_POST['senha'];

    // Verifica conexão
    if (!isset($conn)) {
        die("Erro: Conexão com banco de dados não encontrada.");
    }

    // CONSULTA SIMPLES (Sem Hash)
    $sql = "SELECT * FROM tbusuarios WHERE email = ? AND senha = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $email_login, $senha_login);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $linha = $resultado->fetch_assoc();

        if ($resultado->num_rows > 0) {
            // Sucesso! Define as variáveis de sessão
            $_SESSION['logado']         = TRUE; 
            $_SESSION['login_usuario']  = $linha['email'];
            $_SESSION['nome_usuario']   = $linha['nome'];
            
            // LÊ O NÍVEL DIRETAMENTE DO BANCO DE DADOS (admin ou comum)
            $_SESSION['nivel_usuario']  = $linha['nivel_usuario']; 
            
            $_SESSION['nome_da_sessao'] = session_name();
            
            // Redireciona para a Área Administrativa
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
    <!-- CSS específico -->
    <link rel="stylesheet" href="/ProjetoTransportePublico/css/meu_estilo.css">

    <!-- Fonte local -->
    <link rel="stylesheet" href="/ProjetoTransportePublico/css/fonts.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/ProjetoTransportePublico/css/bootstrap.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="/ProjetoTransportePublico/css/bootstrap-icons.css">
</head>

<style>
    body { 
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0; 
            font-family: 'Segoe UI', sans-serif; 
        }
    .afastamento{
        margin-bottom: 80px;
    }
</style>


<body class="bg-light">
 <?php 
include '../menu.php'; 
?>
<div class="container">
    <div class="row justify-content-center mt-5">
        
        <div class="col-md-6 col-lg-4 bg-white p-4 shadow rounded mt-5 afastamento">
            
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
 
<script src="/ProjetoTransportePublico/js/bootstrap.bundle.min.js"></script>
<?php 
include '../rodape.php'; 
?>
</body>

</html>