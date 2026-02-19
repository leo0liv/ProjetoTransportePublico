<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Refresh" content="15;URL=login.php">
    <title>Atenção - Acesso Negado</title>
    <script src="https://kit.fontawesome.com/2495680ceb.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body style="background: black; background: linear-gradient(to bottom, #000000 0%, #590000 60%, #ff4500 100%); height: 100vh; display: flex; align-items: center; justify-content: center; font-family: sans-serif; overflow: hidden;">

    <div class="container" style="max-width: 450px; text-align: center;">
        
        <div style="background-color: white; padding: 15px; margin-bottom: 30px; border-radius: 5px; box-shadow: 0 5px 15px rgba(0,0,0,0.5);">
            <h1 style="margin: 0; color: #8a3c3c; font-weight: bold; text-transform: uppercase; font-size: 36px;">Atenção!</h1>
        </div>

        <div style="background-color: white; border-radius: 5px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.8);">
            
            <div style="padding: 40px 0; background-color: white;">
                <span class="fa-stack fa-5x" style="font-size: 8em;">
                    <i class="fas fa-user-secret fa-stack-1x" style="color: #333;"></i>
                    <i class="fas fa-ban fa-stack-2x" style="color: #c9302c; opacity: 0.9;"></i>
                </span>
            </div>

            <div style="background-color: #f2dede; padding: 20px; border-top: 5px solid #ebccd1;">
                
                <h4 style="color: #a94442; margin-bottom: 25px; font-weight: bold;">
                    <i class="fas fa-spinner fa-pulse"></i> Usuário e/ou Senha Inválido
                </h4>

                <div style="display: flex; justify-content: center; gap: 10px;">
                    
                    <a href="login.php" style="
                        display: inline-block;
                        width: 120px;
                        height: 120px;
                        background-color: #d9534f;
                        color: white;
                        border-radius: 5px;
                        text-decoration: none;
                        padding-top: 20px;
                        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
                    ">
                        <i class="fas fa-external-link-alt fa-3x" style="transform: rotate(180deg); display: block; margin-bottom: 10px;"></i>
                        <span style="font-weight: bold; font-size: 14px; line-height: 1.2; display: block;">Tentar<br>novamente</span>
                    </a>

                    <a href="../index.php" style="
                        display: inline-block;
                        width: 120px;
                        height: 120px;
                        background-color: #5cb85c;
                        color: white;
                        border-radius: 5px;
                        text-decoration: none;
                        padding-top: 20px;
                        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
                    ">
                        <i class="fas fa-home fa-3x" style="display: block; margin-bottom: 10px;"></i>
                        <span style="font-weight: bold; font-size: 14px; line-height: 1.2; display: block;">Voltar<br>Área Pública</span>
                    </a>

                </div>

                <p style="color: #a94442; font-size: 11px; margin-top: 20px;">
                    Caso não faça uma escolha em 15 segundos será<br>redirecionado automaticamente.
                </p>

            </div>
        </div>

    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>    
</body>
</html>