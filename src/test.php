<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Rotas de Ônibus</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />

    <style>
        #map {
            height: 600px;
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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

<div class="container my-4">
    <div class="row mb-3">
        <div class="col">
            <h2><i class="bi bi-bus-front"></i> Sistema de Rotas de Ônibus</h2>
            <p class="text-muted">Visualização de linhas e pontos utilizando Leaflet e OSRM.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div id="map"></div>
        </div>

        <div class="col-md-4">
            <div class="sidebar border">
                <h4>Legenda</h4>
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item">🔵 Ponto de Início (A)</li>
                    <li class="list-group-item">🔴 Ponto de Destino (B)</li>
                </ul>
                <hr>
                <div id="instructions">
                    <p class="small">As instruções detalhadas da rota aparecerão aqui assim que o mapa carregar.</p>
                </div>
            </div>
        </div>
    </div>
</div>

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
            styles: [{ color: '#007bff', opacity: 0.7, weight: 6 }]
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
            <h6>Passo a passo:</h6>
            <ol class="small">
                ${routes[0].instructions.map(inst => `<li>${inst.text}</li>`).join('')}
            </ol>
        `;
    });
</script>

</body>
</html>