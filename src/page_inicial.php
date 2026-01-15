<?php
// Inclui o arquivo de conexão com o banco de dados
include '../connections/db_connect.php';

// Função utilitária para retornar um valor COUNT(*)
function queryValue($conn, $sql) {
    return $conn->query($sql)->fetch_assoc()['t'] ?? 0;
}

// Estatísticas gerais do sistema
$stats = [
    'linhas'            => queryValue($conn, "SELECT COUNT(*) AS t FROM tblinhas"),
    'veiculos_ativos'   => queryValue($conn, "SELECT COUNT(*) AS t FROM tbveiculos"),
    'pontos'            => queryValue($conn, "SELECT COUNT(*) AS t FROM tbpontos"),
    'motoristas_ativos' => queryValue($conn, "SELECT COUNT(*) AS t FROM tbmotoristas"),
];

// Consulta das últimas localizações registradas em tempo real
$sqlLocal = "SELECT l.*, v.placa, lin.codigo AS linha_codigo
             FROM tblocalizacao_tempo_real l
             JOIN tbveiculos v ON l.id_veiculo = v.id_veiculo
             LEFT JOIN tblinhas lin ON v.id_linha = lin.id_linha
             ORDER BY l.timestamp_atualizacao DESC
             LIMIT 5
";

// Executa a busca e retorna como array associativo
$localizacoes = $conn->query($sqlLocal)->fetch_all(MYSQLI_ASSOC);
?>
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

    <!-- MAPS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />

    <style>
    #map {
        height: 600px;
        width: 100%;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .sidebar {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        height: 600px;
        overflow-y: auto;
    }
    </style>

</head>

<body>
    <main class="container py-4">
        <h1 class="mb-4">
            <i class="bi bi-speedometer2"></i> Monitoramento em Tempo Real
        </h1>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-signpost-2 fs-1 text-primary"></i>
                        <h3 class="mt-2 mb-0"><?php echo $stats['linhas']; ?></h3>
                        <p class="text-muted mb-0">Linhas</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-bus-front fs-1 text-success"></i>
                        <h3 class="mt-2 mb-0"><?php echo $stats['veiculos_ativos']; ?></h3>
                        <p class="text-muted mb-0">Veículos Ativos</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-geo-alt fs-1 text-warning"></i>
                        <h3 class="mt-2 mb-0"><?php echo $stats['pontos']; ?></h3>
                        <p class="text-muted mb-0">Pontos</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-person-badge fs-1 text-info"></i>
                        <h3 class="mt-2 mb-0"><?php echo $stats['motoristas_ativos']; ?></h3>
                        <p class="text-muted mb-0">Motoristas Ativos</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div id="map" style="height: 500px;"></div>
                <div id="instructions">
                    <p class="small text-muted">
                    </p>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-clock-history"></i> Últimas Atualizações
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php if (empty($localizacoes)): ?>
                            <li class="list-group-item text-center text-muted py-4">
                                Nenhuma localização registrada.
                            </li>
                            <?php else: ?>
                            <?php foreach ($localizacoes as $loc): ?>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo $loc['placa']; ?></strong>
                                        <?php if ($loc['linha_codigo']): ?>
                                        <span class="badge bg-primary ms-1"><?php echo $loc['linha_codigo']; ?></span>
                                        <?php endif; ?>
                                        <br>
                                        <small class="text-muted">
                                            <?php echo number_format($loc['velocidade'], 1); ?> km/h
                                        </small>
                                    </div>
                                    <small class="text-muted">
                                        <?php echo date('H:i', strtotime($loc['timestamp_atualizacao'])); ?>
                                    </small>
                                </div>
                            </li>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="../js/bootstrap.bundle.min.js"></script>

    <!-- MAPS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

    <script>
    // 1. Inicializar o Mapa (Coordenadas de exemplo: São Paulo)
    const map = L.map('map').setView([-23.5505, -46.6333], 13);

    // 2. Adicionar Camada do OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // 3. Definir Pontos de Ônibus (Latitude e Longitude)
    const pontoA = L.latLng(-23.5505, -46.6333); // Ex: Praça da Sé
    const pontoB = L.latLng(-23.5611, -46.6559); // Ex: MASP (Av. Paulista)

    // 4. Configurar o Roteamento (OSRM)
    const control = L.Routing.control({
        waypoints: [
            pontoA,
            pontoB
        ],
        lineOptions: {
            styles: [{
                color: '#007bff',
                opacity: 0.7,
                weight: 6
            }]
        },
        createMarker: function(i, waypoint, n) {
            // Personaliza os marcadores de ponto
            const markerLabel = i === 0 ? "Ponto Inicial" : "Ponto Final";
            return L.marker(waypoint.latLng).bindPopup(markerLabel);
        },
        router: L.Routing.osrmv1({
            serviceUrl: `https://router.project-osrm.org/route/v1`
        })
    }).addTo(map);

    // 5. Mover as instruções para a nossa barra lateral do Bootstrap
    control.on('routesfound', function(e) {
        const routes = e.routes;
        const summary = routes[0].summary;
        const container = document.getElementById('instructions');

        container.innerHTML = `
                <div class="alert alert-info">
                    <strong>Distância:</strong> ${(summary.totalDistance / 1000).toFixed(2)} km <br>
                    <strong>Tempo estimado:</strong> ${Math.round(summary.totalTime / 60)} min
                </div>
            `;
    });
    </script>

</body>

</html>