<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Refresh" content="15;URL=login.php">
    <title>Atenção - Acesso Negado</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/ProjetoTransportePublico/css/bootstrap.css">
    <script src="https://kit.fontawesome.com/2495680ceb.js" crossorigin="anonymous"></script>
</head>
<body class="d-flex align-items-center justify-content-center vh-100 m-0" 
      style="background: radial-gradient(circle at top, #2c3e50 0%, #e9ecef 100%); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow: hidden;">

    <div class="container" style="max-width: 450px;">
        <div class="text-center">
            
            <div class="bg-white p-3 mb-4 rounded shadow-sm border-bottom border-4" style="border-color: #3b71ca !important;">
                <h1 class="m-0 fw-bold text-uppercase" style="color: #2c3e50; font-size: 32px; letter-spacing: -1px;">Atenção!</h1>
            </div>

            <div class="bg-white rounded overflow-hidden shadow-lg border">
                
                <div class="py-5 bg-light">
                    <span class="fa-stack fa-4x" style="font-size: 5.5em;">
                        <i class="fas fa-user-lock fa-stack-1x text-secondary"></i>
                        <i class="fas fa-ban fa-stack-2x text-danger" style="opacity: 0.8;"></i>
                    </span>
                </div>

                <div class="p-4 bg-white border-top">
                    
                    <h5 class="text-dark fw-bold mb-4">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>Usuário e/ou Senha Inválido
                    </h5>

                    <div class="d-flex justify-content-center gap-3">
                        
                        <a href="login.php" class="btn btn-primary d-flex flex-column align-items-center justify-content-center shadow-sm border-0" 
                           style="width: 120px; height: 120px; background-color: #3b71ca;">
                            <i class="fas fa-redo-alt fa-2x mb-2"></i>
                            <span class="fw-bold small">Tentar<br>novamente</span>
                        </a>

                        <a href="../index.php" class="btn btn-dark d-flex flex-column align-items-center justify-content-center shadow-sm border-0" 
                           style="width: 120px; height: 120px; background-color: #2c3e50;">
                            <i class="fas fa-map-marked-alt fa-2x mb-2"></i>
                            <span class="fw-bold small">Voltar ao<br>Mapa</span>
                        </a>

                    </div>

                    <div class="mt-4">
                        <div class="progress" style="height: 4px;">
                            <div id="progresso" class="progress-bar bg-primary" style="width: 100%; transition: width 15s linear;"></div>
                        </div>
                        <p class="text-muted mt-2 mb-0" style="font-size: 11px;">
                            Redirecionando automaticamente em instantes...
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        // Pequena animação visual para a barra de progresso
        window.onload = function() {
            setTimeout(() => {
                document.getElementById('progresso').style.width = '0%';
            }, 100);
        };
    </script>
    <!-- Bootstrap JS -->
    <script src="/ProjetoTransportePublico/js/bootstrap.bundle.min.js"></script>
</body>
</html>