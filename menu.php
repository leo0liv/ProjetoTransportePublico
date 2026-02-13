<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Público</title>

    <!-- CSS específico -->
    <link rel="stylesheet" href="./css/meu_estilo.css">

    <!-- Fonte local -->
    <link rel="stylesheet" href="./css/fonts.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="./css/bootstrap.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="./css/bootstrap-icons.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark">
        <div class="container-fluid container-lg">
          <a class="navbar-brand" href="./index.php">
            <strong class="bi bi-bus-front text-warning"></strong>
            <strong>Transporte Público</strong>
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mx-auto">
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="index.php"><i class="bi bi-geo-alt"></i>&nbsp;Mapa</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="linhas.php"><i class="bi bi-signpost-2"></i>&nbsp;Linhas</a>
              </li>
            </ul>
            <div class="d-flex">
                <form action="linhas_buscar.php" method="get" name="form_busca" id="form_busca" class="d-flex" role="search">
                    <input 
                        type="text"
                        class="form-control me-2"  
                        placeholder="Buscar por linha."
                        name="buscar"
                        id="buscar"
                        required
                    />
                    
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-search"></i>
                    </button>&nbsp;
                </form>
                <a href="./admin/adm_options.php" class="btn btn-secondary btn-info">
                    <strong class="bi bi-gear text-white">&nbsp;Admin</strong>
                </a>
            </div>
          </div>
        </div>
      </nav>

    <!-- Bootstrap JS -->
    <script src="./js/bootstrap.bundle.min.js"></script>
</body>
</html>