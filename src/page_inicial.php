<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Inicial</title>

    <!-- CSS específico -->
    <link rel="stylesheet" href="../css/meu_estilo.css">

    <!-- Fonte local -->
    <link rel="stylesheet" href="../css/fonts.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../css/bootstrap.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="../css/bootstrap-icons.css">
</head>

<body>
    <main class="container py-4">
        <h1 class="mb-4">
            <i class="bi bi-speedometer2"></i> Monitoramento em Tempo Real
        </h1>

        <!-- Cards -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-signpost-2 fs-1 text-primary"></i>
                        <h3 class="mt-2 mb-0"></h3>
                        <p class="text-muted mb-0">Linhas</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-bus-front fs-1 text-success"></i>
                        <h3 class="mt-2 mb-0"></h3>
                        <p class="text-muted mb-0">Veículos Ativos</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-geo-alt fs-1 text-warning"></i>
                        <h3 class="mt-2 mb-0"></h3>
                        <p class="text-muted mb-0">Pontos</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-person-badge fs-1 text-info"></i>
                        <h3 class="mt-2 mb-0"></h3>
                        <p class="text-muted mb-0">Motoristas Ativos</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <!-- Mapa Simulado -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-map"></i> Mapa de Localização
                    </div>
                    <div class="card-body">
                        <div class="map-container d-flex align-items-center justify-content-center">
                            <div class="text-center text-muted">
                                <i class="bi bi-map fs-1"></i>
                                <p class="mt-2">Integre com Google Maps ou Leaflet para visualização real</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Últimas Localizações -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-clock-history"></i> Últimas Atualizações
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item text-center text-muted py-4">
                            </li>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>#</strong>

                                        <span class="badge bg-primary ms-1">#</span>
                                        <br>
                                        <small class="text-muted">
                                        </small>
                                    </div>
                                    <small class="text-muted">
                                    </small>
                                </div>
                            </li>
                        </ul>
                    </div> 
                </div>
            </div>
        </div>
    </main>


    <!-- Bootstrap JS -->
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>