<?php
// Nota: A variável $titulo_pagina deve ser definida no topo de cada página.
$titulo = $titulo_pagina ?? "Transporte Público - Admin"; 
// Este arquivo assume que session_start() já foi chamado (por exemplo, dentro de verificar_login.php)
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>

    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/meu_estilo.css">
    <link rel="stylesheet" href="../css/fonts.css"> 
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="adm_options.php">
        <i class="bi bi-bus-front text-primary"></i> RB Admin
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        
        <li class="nav-item">
          <a class="nav-link" href="linhas.php">
            <i class="bi bi-signpost-2-fill"></i> Linhas
          </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="rotas_lista.php">
              <i class="bi bi-diagram-3-fill"></i> Rotas
            </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="veiculos_lista.php">
            <i class="bi bi-bus-front-fill"></i> Veículos
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="pontos_lista.php">
            <i class="bi bi-geo-alt-fill"></i> Pontos
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="motoristas_lista.php">
            <i class="bi bi-person-badge-fill"></i> Motoristas
          </a>
        </li>
        
        <?php if (isset($_SESSION['nivel_usuario']) && $_SESSION['nivel_usuario'] === 'admin'): ?>
        <li class="nav-item">
            <a class="nav-link text-info fw-bold" href="usuarios_lista.php">
                <i class="bi bi-people-fill"></i> Gerenciar Usuários
            </a>
        </li>
        <?php endif; ?>

      </ul>
      
      <ul class="navbar-nav">
          <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                 <i class="bi bi-person-circle"></i> Olá, <?php echo $_SESSION['nome_usuario'] ?? 'Visitante'; ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="../index.php"><i class="bi bi-house-door-fill"></i> Ir para o Site Público</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="logout.php">
                        <i class="bi bi-box-arrow-right"></i> Sair / Logout
                    </a>
                </li>
              </ul>
          </li>
      </ul>
    </div>
  </div>
</nav>

<main class="container" style="padding-top: 20px;">

    </main>
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>