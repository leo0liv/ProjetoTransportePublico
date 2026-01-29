<?php
require_once '../connections/db_connect.php';

$id_linha = 1; 

// Usando o padrão mysqli conforme seu arquivo de conexão
$sql = "SELECT p.nome, p.latitude, p.longitude 
        FROM tbrotas r
        JOIN tbpontos p ON r.id_ponto = p.id_ponto
        WHERE r.id_linha = ?
        ORDER BY r.ordem ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_linha); // "i" significa que o ID é um número inteiro
$stmt->execute();
$result = $stmt->get_result();
$pontos = $result->fetch_all(MYSQLI_ASSOC);

// Converte para JSON para o JavaScript usar
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
        const dadosDoBanco = <?php echo $pontosJSON; ?>;

        // Se o banco estiver vazio, evita erro no mapa
        if (dadosDoBanco.length === 0) {
            console.error("Nenhum ponto encontrado para esta linha no banco de dados.");
        }

        const map = L.map('map').setView([-23.5916, -48.0530], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        if (dadosDoBanco.length >= 2) {
            const waypoints = dadosDoBanco.map(p => L.latLng(p.latitude, p.longitude));

            L.Routing.control({
                waypoints: waypoints,
                language: 'pt-BR',
                createMarker: function(i, waypoint, n) {
                    return L.marker(waypoint.latLng).bindPopup(`<b>${dadosDoBanco[i].nome}</b>`);
                }
            }).addTo(map);
        }
    </script>
</body>
</html>