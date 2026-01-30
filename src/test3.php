<?php
require_once '../connections/db_connect.php';

// Busca todas as linhas para o select
$resLinhas = $conn->query("SELECT id_linha, codigo, nome FROM tblinhas ORDER BY nome");
$linhas = $resLinhas->fetch_all(MYSQLI_ASSOC);

// Pega a linha selecionada (ou a primeira por padrão)
$id_selecionado = isset($_GET['linha']) ? int_param($_GET['linha']) : (isset($linhas[0]) ? $linhas[0]['id_linha'] : 0);

// Busca os pontos incluindo o novo campo tipo_ponto
$sql = "SELECT p.nome, p.latitude, p.longitude, p.tipo_ponto 
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
        // Dados injetados pelo PHP a partir da consulta SQL nas tabelas tbpontos e tbrotas
        const dadosDoBanco = <?php echo $pontosJSON; ?>;

        // Configuração dos Ícones Coloridos
        const icones = {
            'inicio': new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
            }),
            'meio': new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
            }),
            'fim': new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
            })
        };

        // Inicializa o mapa centralizado em Itapetininga
        const map = L.map('map').setView([-23.5916, -48.0530], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        if (dadosDoBanco.length >= 2) {
            // Mapeia as coordenadas de latitude e longitude da tabela tbpontos
            const waypoints = dadosDoBanco.map(p => L.latLng(p.latitude, p.longitude));

            L.Routing.control({
                waypoints: waypoints,
                language: 'pt-BR',
                routeWhileDragging: false,
                createMarker: function(i, waypoint, n) {
                    // TRATAMENTO DE TEXTO: Remove espaços e ignora maiúsculas/minúsculas
                    const tipoRaw = dadosDoBanco[i].tipo_ponto || 'meio';
                    const tipo = tipoRaw.trim().toLowerCase();
                    const nome = dadosDoBanco[i].nome;
                    
                    // Seleção do ícone baseada no valor da coluna tipo_ponto
                    const iconeEscolhido = icones[tipo] || icones['meio'];

                    // Log para verificar no F12 se o tipo "fim" está sendo lido corretamente
                    console.log(`Ponto ${i}: ${nome} | Tipo identificado: ${tipo}`);

                    return L.marker(waypoint.latLng, { icon: iconeEscolhido })
                            .bindPopup(`<b>${nome}</b><br>Tipo: ${tipo.toUpperCase()}`);
                }
            }).addTo(map);
        }
    </script>
</html>