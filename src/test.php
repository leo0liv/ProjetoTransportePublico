<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Monitoramento TI_19</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <style>
        #map { height: calc(100vh - 60px); width: 100%; border-radius: 8px; }
        .sidebar { height: calc(100vh - 60px); overflow-y: auto; background: #fff; box-shadow: 2px 0 5px rgba(0,0,0,0.1); }
        .bus-label { background: white; border: 1px solid #333; padding: 2px 5px; border-radius: 4px; font-weight: bold; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark shadow-sm">
    <div class="container-fluid"><span class="navbar-brand">📍 GPS Transporte TI_19</span></div>
</nav>

<div class="container-fluid mt-2">
    <div class="row">
        <div class="col-md-3 sidebar p-3">
            <h6 class="text-uppercase text-muted fw-bold">Selecione uma Linha</h6>
            <div id="lista-linhas" class="list-group list-group-flush"></div>
        </div>
        <div class="col-md-9">
            <div id="map"></div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

<script>
    const map = L.map('map').setView([-23.59, -48.05], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    let routingControl = null;
    let stopMarkers = [];
    let vehicleMarkers = {}; // Guarda os marcadores dos ônibus pela placa
    let activeLineId = null;
    let refreshTimer = null;

    const busIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/3448/3448339.png',
        iconSize: [35, 35],
        iconAnchor: [17, 17]
    });

    // 1. Carregar lista de linhas ao iniciar
    async function init() {
        const res = await fetch('get_dados.php?action=get_linhas');
        const linhas = await res.json();
        const lista = document.getElementById('lista-linhas');

        linhas.forEach(linha => {
            const btn = document.createElement('button');
            btn.className = "list-group-item list-group-item-action border-0 mb-1 rounded";
            btn.innerHTML = `🚌 ${linha.nome}`;
            btn.onclick = () => selecionarLinha(linha);
            lista.appendChild(btn);
        });
    }

    // 2. Selecionar e desenhar rota
    function selecionarLinha(linha) {
        activeLineId = linha.id;
        
        // Limpar tudo
        if (routingControl) map.removeControl(routingControl);
        stopMarkers.forEach(m => map.removeLayer(m));
        Object.values(vehicleMarkers).forEach(m => map.removeLayer(m));
        vehicleMarkers = {};
        stopMarkers = [];

        // Desenhar pontos e rota
        const wps = linha.pontos.map(p => {
            const m = L.circleMarker([p.lat, p.lng], {radius: 6, color: 'red'}).addTo(map).bindPopup(p.nome);
            stopMarkers.push(m);
            return L.latLng(p.lat, p.lng);
        });

        routingControl = L.Routing.control({
            waypoints: wps,
            createMarker: () => null,
            addWaypoints: false,
            lineOptions: { styles: [{ color: '#2c3e50', weight: 4 }] },
            show: false
        }).addTo(map);

        map.fitBounds(L.latLngBounds(wps));

        // Iniciar Tempo Real
        if (refreshTimer) clearInterval(refreshTimer);
        updateVehicles();
        refreshTimer = setInterval(updateVehicles, 5000); // Atualiza a cada 5s
    }

    // 3. Atualizar posição dos veículos
    async function updateVehicles() {
        if (!activeLineId) return;

        const res = await fetch(`get_dados.php?action=get_veiculos&id_linha=${activeLineId}`);
        const veiculos = await res.json();

        veiculos.forEach(v => {
            const pos = [v.latitude, v.longitude];
            if (vehicleMarkers[v.placa]) {
                vehicleMarkers[v.placa].setLatLng(pos); // Move o ônibus
            } else {
                const m = L.marker(pos, {icon: busIcon}).addTo(map)
                           .bindTooltip(v.placa, {permanent: true, direction: 'top', className: 'bus-label'});
                vehicleMarkers[v.placa] = m;
            }
        });
    }

    init();
</script>
</body>
</html>