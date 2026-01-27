<?php
// 1. INICIA A SESSÃO (DEVE SER A PRIMEIRA LINHA EXECUTÁVEL)
session_start();

// 2. VERIFICA SE O USUÁRIO JÁ ESTÁ LOGADO
// Se estiver, redireciona para o painel principal, evitando que veja a tela de login.
if (isset($_SESSION['logado']) && $_SESSION['logado'] === TRUE) {
    // Redireciona para o Painel Administrativo que você deseja
    header("Location: adm_options.php"); 
    exit();
}

// 3. Inclui a conexão (use o nome do seu arquivo de conexão)
// Removi o include 'verificar_login.php' daqui!
include '../connections/db_connect.php'; 

$mensagem = '';
$tipo_alerta = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 4. Recebe e sanitiza os dados
    $email = $conn->real_escape_string($_POST['email']);
    $senha_digitada = $_POST['senha']; 
    
    // 5. Busca o usuário pelo e-mail
    $sql = "SELECT id_usuario, nome, email, senha_hash FROM tbusuarios WHERE email = ?";
    
    // Usando prepared statements para maior segurança
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows == 1) {
        $usuario = $resultado->fetch_assoc();
        $senha_hash_bd = $usuario['senha_hash'];
        
        // 6. Verifica a senha usando password_verify()
        if ($senha_digitada == $senha_hash_bd) {
            
            // Sucesso no login: Cria as variáveis de sessão
            $_SESSION['logado'] = TRUE;
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nome_usuario'] = $usuario['nome'];
            
            // Redireciona para a área administrativa após o login
            header("Location: adm_options.php"); 
            exit();
            
        } else {
            $mensagem = "Senha incorreta. Tente novamente.";
            $tipo_alerta = 'warning';
        }
    } else {
        $mensagem = "Usuário não encontrado.";
        $tipo_alerta = 'danger';
    }

    $stmt->close();
}

// Fecha a conexão, se estiver aberta
if (isset($conn)) {
    $conn->close();
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

            <?php if ($mensagem): ?>
                <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
                    <?php echo $mensagem; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

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