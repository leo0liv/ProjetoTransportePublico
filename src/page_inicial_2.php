<?php
include '../connections/db_connect.php';

// Função utilitária
function queryValue($conn, $sql) {
    $res = $conn->query($sql);
    return ($res && $res->num_rows > 0) ? $res->fetch_assoc()['t'] : 0;
}

// 1. Estatísticas
$stats = [
    'linhas'            => queryValue($conn, "SELECT COUNT(*) AS t FROM tblinhas"),
    'veiculos_ativos'   => queryValue($conn, "SELECT COUNT(*) AS t FROM tbveiculos"),
    'pontos'            => queryValue($conn, "SELECT COUNT(*) AS t FROM tbpontos"),
    'motoristas_ativos' => queryValue($conn, "SELECT COUNT(*) AS t FROM tbmotoristas"),
];

// 2. Busca Veículos em Tempo Real
$sqlLocal = "SELECT l.*, v.placa, lin.codigo AS linha_codigo
             FROM tblocalizacao_tempo_real l
             JOIN tbveiculos v ON l.id_veiculo = v.id_veiculo
             LEFT JOIN tblinhas lin ON v.id_linha = lin.id_linha
             ORDER BY l.timestamp_atualizacao DESC";
$veiculos = $conn->query($sqlLocal)->fetch_all(MYSQLI_ASSOC);

// 3. Busca a Rota da Linha 1 (Pontos ordenados para o traçado)
$sqlRota = "SELECT p.latitude, p.longitude, p.nome 
            FROM tbrotas r
            JOIN tbpontos p ON r.id_ponto = p.id_ponto
            WHERE r.id_linha = 1 
            ORDER BY r.ordem ASC";
$rotaResult = $conn->query($sqlRota);
$caminhoRota = [];
while($row = $rotaResult->fetch_assoc()){
    $caminhoRota[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Monitoramento com Rotas - TI_19</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    
    <style>
        #map { height: 600px; width: 100%; border-radius: 8px; }
        /* Esconde o painel de texto do roteamento para não poluir o mapa */
        .leaflet-routing-container { display: none; }
    </style>
</head>
<body>

<main class="container py-4">
    <h1 class="mb-4"><i class="bi bi-map"></i> Mapa de Rotas e Veículos</h1>

    <div class="row g-4">
        <div class="col-lg-9">
            <div id="map"></div>
            <div id="info-rota" class="mt-2"></div>
        </div>

        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">Status dos Veículos</div>
                <div class="list-group list-group-flush">
                    <?php foreach ($veiculos as $v): ?>
                    <div class="list-group-item">
                        <strong><?=$v['placa']?></strong> (<?=$v['linha_codigo']?>)<br>
                        <small class="text-success"><i class="bi bi-speedometer"></i> <?=$v['velocidade']?> km/h</small>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

<script>
    const map = L.map('map').setView([-23.5916, -48.0530], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    // 1. RENDERIZAR A LINHA DA ROTA (Traçado das ruas)
    // Pegamos os pontos que vieram do banco (tbrotas + tbpontos)
    const pontosDaRota = <?php echo json_encode($caminhoRota); ?>;
    
    if (pontosDaRota.length > 0) {
        // Converte os pontos para o formato do Leaflet Routing
        const waypoints = pontosDaRota.map(p => L.latLng(p.latitude, p.longitude));

        const control = L.Routing.control({
            waypoints: waypoints,
            router: L.Routing.osrmv1({
                serviceUrl: `https://router.project-osrm.org/route/v1`
            }),
            lineOptions: {
                styles: [{ color: '#007bff', opacity: 0.6, weight: 6 }]
            },
            createMarker: function(i, wp) {
                // Cria marcadores menores para os pontos de ônibus na rota
                return L.marker(wp.latLng).bindPopup(`Ponto: ${pontosDaRota[i].nome}`);
            },
            addWaypoints: false,
            draggableWaypoints: false
        }).addTo(map);

        // Captura distância e tempo da rota para exibir na tela
        control.on('routesfound', function(e) {
            const summary = e.routes[0].summary;
            document.getElementById('info-rota').innerHTML = `
                <div class="alert alert-secondary">
                    <i class="bi bi-info-circle"></i> 
                    Linha 101-A: ${(summary.totalDistance / 1000).toFixed(2)} km | 
                    Tempo estimado: ${Math.round(summary.totalTime / 60)} min
                </div>`;
        });
    }

    // 2. RENDERIZAR VEÍCULOS (Posição atual)
    const veiculos = <?php echo json_encode($veiculos); ?>;
    const busIcon = L.divIcon({
        html: '<i class="bi bi-bus-front-fill" style="font-size: 24px; color: #198754; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);"></i>',
        className: 'custom-bus', iconSize: [24, 24], iconAnchor: [12, 24]
    });

    veiculos.forEach(v => {
        L.marker([v.latitude, v.longitude], {icon: busIcon})
            .addTo(map)
            .bindPopup(`<b>Ônibus: ${v.placa}</b><br>Velocidade: ${v.velocidade} km/h`);
    });
</script>

</body>
</html>