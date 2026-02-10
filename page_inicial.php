<?php
// CONEXÃO E FUNÇÕES
require_once './connections/db_connect.php';

function queryValue($conn, $sql) {
    $res = $conn->query($sql);
    return ($res) ? ($res->fetch_assoc()['t'] ?? 0) : 0;
}

function int_param($v) { return (int)$v; }

<<<<<<< HEAD
// ESTATÍSTICAS
$stats = [
    'linhas'            => queryValue($conn, "SELECT COUNT(*) AS t FROM tblinhas"),
    'veiculos_ativos'   => queryValue($conn, "SELECT COUNT(*) AS t FROM tbveiculos"),
    'pontos'            => queryValue($conn, "SELECT COUNT(*) AS t FROM tbpontos"),
    'motoristas_ativos' => queryValue($conn, "SELECT COUNT(*) AS t FROM tbmotoristas"),
];
=======
// ESTATÍSTICAS (Cards Superiores)
//$stats = [
//    'linhas'            => queryValue($conn, "SELECT COUNT(*) AS t FROM tblinhas"),
//    'veiculos_ativos'   => queryValue($conn, "SELECT COUNT(*) AS t FROM tbveiculos"),
//    'pontos'            => queryValue($conn, "SELECT COUNT(*) AS t FROM tbpontos"),
//    'motoristas_ativos' => queryValue($conn, "SELECT COUNT(*) AS t FROM tbmotoristas"),
//];
>>>>>>> 7d6a09bc65690615348b2fe4640f62a5f2544a34

// 1. CARREGAR LISTA DE LINHAS
$resLinhas = $conn->query("SELECT id_linha, codigo, nome FROM tblinhas ORDER BY nome");
$linhas = $resLinhas->fetch_all(MYSQLI_ASSOC);

$id_selecionado = isset($_GET['linha']) ? int_param($_GET['linha']) : (isset($linhas[0]) ? $linhas[0]['id_linha'] : 0);

$pontosArray = [];
$info_horario = null;

if ($id_selecionado > 0) {
    
    // TENTATIVA 1: Buscar o primeiro horário que TENHA pontos cadastrados (Prioridade)
    $sqlHorario = "SELECT h.id_horario, h.horario_partida, h.dia_semana 
                   FROM tbhorario_programados h
                   INNER JOIN tbrotas r ON h.id_horario = r.id_horario
                   WHERE h.id_linha = ? 
                   GROUP BY h.id_horario
                   ORDER BY h.horario_partida ASC LIMIT 1";
                   
    $stmtH = $conn->prepare($sqlHorario);
    $stmtH->bind_param("i", $id_selecionado);
    $stmtH->execute();
    $resHorario = $stmtH->get_result();

    // Se não achar nenhum com pontos, pega qualquer um só para não dar erro (mostra vazio)
    if ($resHorario->num_rows == 0) {
        $stmtH = $conn->prepare("SELECT id_horario, horario_partida, dia_semana FROM tbhorario_programados WHERE id_linha = ? ORDER BY horario_partida ASC LIMIT 1");
        $stmtH->bind_param("i", $id_selecionado);
        $stmtH->execute();
        $resHorario = $stmtH->get_result();
    }

    if ($resHorario->num_rows > 0) {
        $info_horario = $resHorario->fetch_assoc();
        $id_horario_atual = $info_horario['id_horario'];

        // 3. BUSCAR OS PONTOS
// Note que agora pegamos r.tipo_ponto (da tabela tbrotas) e não mais p.tipo_ponto
$sqlRota = "SELECT p.nome, p.latitude, p.longitude, r.tipo_ponto, r.horario_previsto 
            FROM tbrotas r
            JOIN tbpontos p ON r.id_ponto = p.id_ponto
            WHERE r.id_horario = ?
            ORDER BY r.ordem ASC";

        $stmt = $conn->prepare($sqlRota);
        $stmt->bind_param("i", $id_horario_atual);
        $stmt->execute();
        $pontosArray = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

$pontosJSON = json_encode($pontosArray);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Mobilidade</title>
    <link rel="stylesheet" href="./css/meu_estilo.css">
    <link rel="stylesheet" href="./css/bootstrap.css">
    <link rel="stylesheet" href="./css/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        #map { height: 500px; width: 100%; border-radius: 8px; z-index: 1; }
        .scroll-lista { max-height: 440px; overflow-y: auto; }
        /* Estilo para ficar igual sua imagem */
        .list-group-item { border-left: 4px solid transparent; }
        .border-inicio { border-left-color: #198754 !important; } /* Verde */
        .border-meio { border-left-color: #0d6efd !important; }   /* Azul */
        .border-fim { border-left-color: #dc3545 !important; }    /* Vermelho */
    </style>
</head>
<body class="bg-light">

<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-warning"><i class="bi bi-map text-warning"></i> Mapa de Rotas</h1>
        <span class="badge bg-dark">Itapetininga - SP</span>
    </div>

<!-- CARDS 
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
-->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-auto"><label class="fw-bold">Visualizar Itinerário:</label></div>
                <div class="col-md-6">
                    <select name="linha" class="form-select" onchange="this.form.submit()">
                        <?php foreach ($linhas as $l): ?>
                            <option value="<?= $l['id_linha'] ?>" <?= $l['id_linha'] == $id_selecionado ? 'selected' : '' ?>>
                                <?= $l['codigo'] ?> - <?= $l['nome'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if($info_horario): ?>
                    <div class="col-md-4 text-muted small">
                         <i class="bi bi-clock"></i> Saída: <strong><?= substr($info_horario['horario_partida'], 0, 5) ?></strong> (<?= ucfirst($info_horario['dia_semana']) ?>)
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div id="map" class="shadow-sm border"></div>
            <div id="info-rota" class="mt-3"></div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="bi bi-geo-fill text-danger"></i> Logradouros da Rota
                </div>
                
                <div class="p-2 bg-light border-bottom small d-flex justify-content-around">
                    <span><i class="bi bi-circle-fill text-success"></i> Início</span>
                    <span><i class="bi bi-circle-fill text-primary"></i> Ponto</span>
                    <span><i class="bi bi-circle-fill text-danger"></i> Fim</span>
                </div>

                <div class="scroll-lista">
                    <ul class="list-group list-group-flush">
                        <?php if (empty($pontosArray)): ?>
                            <li class="list-group-item text-center py-5 text-muted">
                                Nenhuma viagem ou ponto cadastrado para esta linha ainda.
                                <br><small>Cadastre os pontos no menu "Rotas".</small>
                            </li>
                        <?php else: ?>
                            <?php foreach ($pontosArray as $p): 
                                $tipo = trim(strtolower($p['tipo_ponto']));
                                $classeBorda = 'border-meio';
                                if($tipo == 'inicio') $classeBorda = 'border-inicio';
                                if($tipo == 'fim') $classeBorda = 'border-fim';
                            ?>
                            <li class="list-group-item py-3 <?= $classeBorda ?>">
                                <div class="fw-bold text-dark">
                                    <?= $p['nome'] ?> - <span class="text-muted text-uppercase small"><?= $tipo ?></span>
                                </div>
                                <?php if(!empty($p['horario_previsto'])): ?>
                                    <small class="text-muted"><i class="bi bi-clock"></i> Passagem: <?= substr($p['horario_previsto'], 0, 5) ?></small>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="./js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

<script>
    const map = L.map('map').setView([-23.5916, -48.0530], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);

    const dadosPontos = <?php echo $pontosJSON; ?>;

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
            show: false,
            addWaypoints: false,
            draggableWaypoints: false,
            lineOptions: { styles: [{ color: '#007bff', opacity: 0.7, weight: 6 }] },
            createMarker: function(i, waypoint, n) {
                const p = dadosPontos[i];
                const tipo = (p.tipo_ponto || 'meio').trim().toLowerCase();
                return L.marker(waypoint.latLng, { icon: icones[tipo] || icones['meio'] })
                        .bindPopup(`<b>${p.nome}</b><br>${tipo.toUpperCase()}`);
            }
        }).addTo(map);

        control.on('routesfound', function(e) {
            const s = e.routes[0].summary;
            document.getElementById('info-rota').innerHTML = `
                <div class="alert alert-primary shadow-sm border-0 small">
                    <i class="bi bi-info-circle-fill"></i> 
                    Resumo: ${(s.totalDistance / 1000).toFixed(2)} km e ${Math.round(s.totalTime / 60)} min.
                </div>`;
        });
    } else if (dadosPontos.length === 1) {
        const p = dadosPontos[0];
        L.marker([p.latitude, p.longitude], { icon: icones['meio'] }).addTo(map).bindPopup(p.nome);
        map.setView([p.latitude, p.longitude], 15);
    }
</script>
</body>
</html>