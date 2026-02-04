<?php
// CONEXÃO E FUNÇÕES
require_once '../connections/db_connect.php';

function queryValue($conn, $sql) {
    return $conn->query($sql)->fetch_assoc()['t'] ?? 0;
}

function int_param($v) { return (int)$v; }

// ESTATÍSTICAS (Cards Superiores)
$stats = [
    'linhas'            => queryValue($conn, "SELECT COUNT(*) AS t FROM tblinhas"),
    'veiculos_ativos'   => queryValue($conn, "SELECT COUNT(*) AS t FROM tbveiculos"),
    'pontos'            => queryValue($conn, "SELECT COUNT(*) AS t FROM tbpontos"),
    'motoristas_ativos' => queryValue($conn, "SELECT COUNT(*) AS t FROM tbmotoristas"),
];

// LÓGICA DE SELEÇÃO DE LINHA E BUSCA DA ROTA
$resLinhas = $conn->query("SELECT id_linha, codigo, nome FROM tblinhas ORDER BY nome");
$linhas = $resLinhas->fetch_all(MYSQLI_ASSOC);

$id_selecionado = isset($_GET['linha']) ? int_param($_GET['linha']) : (isset($linhas[0]) ? $linhas[0]['id_linha'] : 0);

$sqlRota = "SELECT p.nome, p.latitude, p.longitude, p.tipo_ponto 
            FROM tbrotas r
            JOIN tbpontos p ON r.id_ponto = p.id_ponto
            WHERE r.id_linha = ?
            ORDER BY r.ordem ASC";

$stmt = $conn->prepare($sqlRota);
$stmt->bind_param("i", $id_selecionado);
$stmt->execute();
$pontosArray = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$pontosJSON = json_encode($pontosArray);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Mobilidade - Itapetininga</title>

        <!-- CSS específico -->
    <link rel="stylesheet" href="../css/meu_estilo.css">

    <!-- Fonte local -->
    <link rel="stylesheet" href="../css/fonts.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../css/bootstrap.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="../css/bootstrap-icons.css">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />

</head>
<body class="bg-light">

<?php include 'menu.php'; ?>

<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-bus-front text-warning"></i> Sistema de Rotas</h1>
        <span class="badge bg-dark">Itapetininga - SP</span>
    </div>

    <div class="row g-4 mb-4">
        <?php 
        $cards = [
            ['Linhas', $stats['linhas'], 'bi-signpost-2', 'primary'],
            ['Veículos', $stats['veiculos_ativos'], 'bi-truck', 'success'],
            ['Pontos', $stats['pontos'], 'bi-geo-alt', 'warning'],
            ['Motoristas', $stats['motoristas_ativos'], 'bi-person-badge', 'info']
        ];
        foreach($cards as $card): ?>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi <?= $card[2] ?> fs-1 text-<?= $card[3] ?>"></i>
                    <h3 class="mt-2 mb-0"><?= $card[1] ?></h3>
                    <p class="text-muted mb-0"><?= $card[0] ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-auto">
                    <label class="fw-bold">Visualizar Itinerário:</label>
                </div>
                <div class="col-md-6">
                    <select name="linha" class="form-select" onchange="this.form.submit()">
                        <?php foreach ($linhas as $l): ?>
                            <option value="<?= $l['id_linha'] ?>" <?= $l['id_linha'] == $id_selecionado ? 'selected' : '' ?>>
                                <?= $l['codigo'] ?> - <?= $l['nome'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div id="map"></div>
            <div id="info-rota" class="mt-3"></div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="bi bi-geo-fill text-danger"></i> Logradouros da Rota
                </div>
                <div class="card-body p-0">
                    <div class="p-3 bg-light border-bottom small d-flex justify-content-around">
                        <span><i class="bi bi-circle-fill text-success"></i> Início</span>
                        <span><i class="bi bi-circle-fill text-primary"></i> Ponto</span>
                        <span><i class="bi bi-circle-fill text-danger"></i> Fim</span>
                    </div>

                    <div class="scroll-lista">
                        <ul class="list-group list-group-flush">
                            <?php if (empty($pontosArray)): ?>
                                <li class="list-group-item text-center py-4 text-muted">Nenhum ponto encontrado.</li>
                            <?php else: ?>
                                <?php foreach ($pontosArray as $p): 
                                    $tipo = trim(strtolower($p['tipo_ponto']));
                                    $classe = 'ponto-meio';
                                    if($tipo == 'inicio') $classe = 'ponto-inicio';
                                    if($tipo == 'fim') $classe = 'ponto-fim';
                                ?>
                                <li class="list-group-item ponto-item <?= $classe ?>">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;"><?= $tipo ?></small>
                                            <div class="fw-bold text-dark"><?= $p['nome'] ?></div>
                                        </div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include 'rodape.php'; ?>

<!-- Bootstrap JS -->
<script src="../js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

<script>
    // Inicializa o Mapa
    const map = L.map('map').setView([-23.5916, -48.0530], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Dados vindos do PHP
    const dadosPontos = <?php echo $pontosJSON; ?>;

    // Ícones Coloridos
    const icones = {
        'inicio': new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41] }),
        'meio':   new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41] }),
        'fim':    new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41] })
    };

    if (dadosPontos.length >= 2) {
        const waypoints = dadosPontos.map(p => L.latLng(p.latitude, p.longitude));

        const control = L.Routing.control({
            waypoints: waypoints,
            language: 'pt-BR',
            show: false, // REMOVE O PAINEL DE TEXTO "VIRE À ESQUERDA"
            addWaypoints: false,
            draggableWaypoints: false,
            lineOptions: { styles: [{ color: '#007bff', opacity: 0.6, weight: 6 }] },
            createMarker: function(i, waypoint, n) {
                const p = dadosPontos[i];
                const tipo = (p.tipo_ponto || 'meio').trim().toLowerCase();
                const icone = icones[tipo] || icones['meio'];
                
                return L.marker(waypoint.latLng, { icon: icone })
                        .bindPopup(`<b>${p.nome}</b>`);
            }
        }).addTo(map);

        // Resumo da rota abaixo do mapa
        control.on('routesfound', function(e) {
            const s = e.routes[0].summary;
            document.getElementById('info-rota').innerHTML = `
                <div class="alert alert-primary shadow-sm border-0">
                    <i class="bi bi-info-circle-fill"></i> 
                    <b>Resumo da Rota:</b> Aproximadamente ${(s.totalDistance / 1000).toFixed(2)} km e ${Math.round(s.totalTime / 60)} minutos de percurso.
                </div>`;
        });
    }
</script>
</body>
</html>