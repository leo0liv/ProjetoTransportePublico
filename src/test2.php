<?php
require_once '../connections/db_connect.php';

// Busca todas as linhas para o select
$resLinhas = $conn->query("SELECT id_linha, codigo, nome FROM tblinhas ORDER BY nome");
$linhas = $resLinhas->fetch_all(MYSQLI_ASSOC);

// Pega a linha selecionada (ou a primeira por padrão)
$id_selecionado = isset($_GET['linha']) ? int_param($_GET['linha']) : (isset($linhas[0]) ? $linhas[0]['id_linha'] : 0);

// Busca os pontos da linha selecionada
$sql = "SELECT p.nome, p.latitude, p.longitude 
        FROM tbrotas r
        JOIN tbpontos p ON r.id_ponto = p.id_ponto
        WHERE r.id_linha = ?
        ORDER BY r.ordem ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_selecionado);
$stmt->execute();
$pontosJSON = json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));

function int_param($v) { return (int)$v; } // Helper simples
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
    <div class="info-box" style="margin-bottom: 15px; padding: 15px; background: #f4f4f4; border-radius: 8px;">
        <form method="GET" id="formLinha">
            <label for="linha"><b>Selecione a Linha de Ônibus:</b></label>
            <select name="linha" id="linha" onchange="this.form.submit()" style="padding: 8px; border-radius: 4px; width: 300px;">
                <?php foreach ($linhas as $l): ?>
                    <option value="<?= $l['id_linha'] ?>" <?= $l['id_linha'] == $id_selecionado ? 'selected' : '' ?>>
                        <?= $l['codigo'] ?> - <?= $l['nome'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

    <script>
        const dadosDoBanco = <?php echo $pontosJSON; ?>;
        
        // Centraliza em Itapetininga
        const map = L.map('map').setView([-23.5916, -48.0530], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        if (dadosDoBanco.length >= 2) {
            const waypoints = dadosDoBanco.map(p => L.latLng(p.latitude, p.longitude));

            L.Routing.control({
                waypoints: waypoints,
                language: 'pt-BR',
                routeWhileDragging: false,
                addWaypoints: false, // Impede o usuário de bagunçar a rota oficial
                createMarker: function(i, waypoint, n) {
                    return L.marker(waypoint.latLng).bindPopup(`<b>${dadosDoBanco[i].nome}</b>`);
                }
            }).addTo(map);
        } else if (dadosDoBanco.length === 1) {
            // Se tiver só um ponto, apenas coloca um marcador
            L.marker([dadosDoBanco[0].latitude, dadosDoBanco[0].longitude])
            .addTo(map)
            .bindPopup(`<b>${dadosDoBanco[0].nome}</b> (Apenas este ponto cadastrado)`);
        }
    </script>
</body>
</html>