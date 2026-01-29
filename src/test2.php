<?php
require_once '../connections/db_connect.php';

// Vamos buscar os pontos da linha ID 1 (exemplo)
$id_linha = 1; 

$sql = "SELECT p.nome, p.latitude, p.longitude 
        FROM tbrotas r
        JOIN tbpontos p ON r.id_ponto = p.id_ponto
        WHERE r.id_linha = :id_linha
        ORDER BY r.ordem ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id_linha' => $id_linha]);
$pontos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Transformamos o array do PHP em JSON para o JavaScript ler
$pontosJSON = json_encode($pontos);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mapa de Rotas - Itapetininga</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
    <style>
        #map { height: 600px; width: 100%; border: 1px solid #ccc; }
    </style>
</head>
<body>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

    <script>
        // Pegamos os dados vindos do banco via PHP
        const dadosDoBanco = <?php echo $pontosJSON; ?>;

        // Inicializa o mapa (centralizado em Itapetininga)
        const map = L.map('map').setView([-23.5916, -48.0530], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        // Converte as coordenadas do banco para o formato do Leaflet
        const waypoints = dadosDoBanco.map(p => L.latLng(p.latitude, p.longitude));

        // Desenha a rota
        L.Routing.control({
            waypoints: waypoints,
            language: 'pt-BR',
            lineOptions: {
                styles: [{ color: '#007bff', weight: 6 }]
            },
            createMarker: function(i, waypoint, n) {
                // Aqui o nome da rua vem direto da sua tabela 'tbpontos'
                const nomeDaRua = dadosDoBanco[i].nome;
                return L.marker(waypoint.latLng).bindPopup(`<b>${nomeDaRua}</b>`);
            }
        }).addTo(map);
    </script>
</body>
</html>