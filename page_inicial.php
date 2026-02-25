<?php
// CONEXÃO E FUNÇÕES
require_once 'connections/db_connect.php';

function int_param($v) { return (int)$v; }

// CARREGAR LISTA DE LINHAS PARA O PRIMEIRO SELECT
$resLinhas = $conn->query("SELECT id_linha, codigo, nome FROM tblinhas ORDER BY nome");
$linhas = $resLinhas->fetch_all(MYSQLI_ASSOC);

// Define a linha selecionada (padrão é a primeira da lista)
$id_selecionado = isset($_GET['linha']) ? int_param($_GET['linha']) : (isset($linhas[0]) ? $linhas[0]['id_linha'] : 0);

$horarios_disponiveis = [];
$id_horario_atual = isset($_GET['horario']) ? int_param($_GET['horario']) : 0;
$pontosArray = [];
$info_horario_selecionado = null;

if ($id_selecionado > 0) {
    // BUSCAR TODOS OS HORÁRIOS DISPONÍVEIS PARA A LINHA SELECIONADA
    $sqlTodosHorarios = "SELECT id_horario, horario_partida, dia_semana 
                         FROM tbhorario_programados 
                         WHERE id_linha = ? 
                         ORDER BY horario_partida ASC";
    $stmtH = $conn->prepare($sqlTodosHorarios);
    $stmtH->bind_param("i", $id_selecionado);
    $stmtH->execute();
    $horarios_disponiveis = $stmtH->get_result()->fetch_all(MYSQLI_ASSOC);

    // Se nenhum horário específico foi selecionado via GET, usa o primeiro disponível
    if ($id_horario_atual == 0 && !empty($horarios_disponiveis)) {
        $id_horario_atual = $horarios_disponiveis[0]['id_horario'];
    }

    // BUSCAR OS PONTOS (LOGRADOUROS) DA ROTA PARA O HORÁRIO SELECIONADO
    if ($id_horario_atual > 0) {
        // Busca info do horário atual para exibir no badge
        foreach($horarios_disponiveis as $h) {
            if($h['id_horario'] == $id_horario_atual) {
                $info_horario_selecionado = $h;
                break;
            }
        }

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
    <title>Painel de Mobilidade - Itapetininga</title>
    <!-- CSS específico -->
    <link rel="stylesheet" href="css/meu_estilo.css">

    <!-- Fonte local -->
    <link rel="stylesheet" href="css/fonts.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="css/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        :root {
            --cor-primaria: #1e3c72; 
            --cor-fundo: #f4f7f6;
        }
        #map { 
            height: 500px; 
            width: 100%; 
            border-radius: 8px; 
            z-index: 1; 
        }
        .scroll-lista { 
            max-height: 440px; 
            overflow-y: auto; 
        }
        .list-group-item { 
            border-left: 4px solid transparent; 
            transition: background 0.2s; 
        }
        .list-group-item:hover { 
            background-color: #f8f9fa; 
        }
        .border-inicio { 
            border-left-color: #198754 !important; 
        } 
        .border-meio { 
            border-left-color: #0d6efd !important; 
        }   
        .border-fim { 
            border-left-color: #dc3545 !important; 
        }
        .cor-primaria{
            color: var(--cor-primaria);
        }   
    </style>
</head>
<body class="bg-light">
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary"><i class="bi bi-map text-primary"></i> Mapa de Rotas</h1>
        <span class="badge bg-dark">Itapetininga - SP</span>
    </div>

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" id="formFiltro" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="fw-bold mb-1">Selecione a Linha:</label>
                    <select name="linha" class="form-select" onchange="document.getElementById('horario_select').value=''; this.form.submit()">
                        <?php foreach ($linhas as $l): ?>
                            <option value="<?= $l['id_linha'] ?>" <?= $l['id_linha'] == $id_selecionado ? 'selected' : '' ?>>
                                <?= $l['codigo'] ?> - <?= $l['nome'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="fw-bold mb-1">Selecione o Horário:</label>
                    <select name="horario" id="horario_select" class="form-select" onchange="this.form.submit()">
                        <?php if (empty($horarios_disponiveis)): ?>
                            <option value="">Nenhum horário cadastrado</option>
                        <?php else: ?>
                            <?php foreach ($horarios_disponiveis as $h): ?>
                                <option value="<?= $h['id_horario'] ?>" <?= $h['id_horario'] == $id_horario_atual ? 'selected' : '' ?>>
                                    <?= ucfirst($h['dia_semana']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <?php if($info_horario_selecionado): ?>
                    <div class="col-md-4">
                        <div class="alert alert-warning m-0 py-2 small border-0">
                             <i class="bi bi-clock-history"></i> Saída: <strong><?= substr($info_horario_selecionado['horario_partida'], 0, 5) ?></strong>
                        </div>
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
                                Nenhuma rota encontrada para este horário.
                                <br><small>Verifique os vínculos na tabela de rotas.</small>
                            </li>
                        <?php else: ?>
                            <?php foreach ($pontosArray as $p): 
                                $tipo = trim(strtolower($p['tipo_ponto']));
                                $classeBorda = 'border-meio';
                                $corIcone = 'text-primary';
                                if($tipo == 'inicio') { $classeBorda = 'border-inicio'; $corIcone = 'text-success'; }
                                if($tipo == 'fim') { $classeBorda = 'border-fim'; $corIcone = 'text-danger'; }
                            ?>
                            <li class="list-group-item py-3 <?= $classeBorda ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold text-dark">
                                            <?= htmlspecialchars($p['nome']) ?>
                                        </div>
                                        <?php if(!empty($p['horario_previsto'])): ?>
                                            <small class="text-muted"><i class="bi bi-clock"></i> Passagem: <?= substr($p['horario_previsto'], 0, 5) ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <button class="btn btn-sm btn-light border rounded-circle shadow-sm" 
                                            onclick="focarNoPonto(<?= $p['latitude'] ?>, <?= $p['longitude'] ?>, '<?= addslashes($p['nome']) ?>')"
                                            title="Ver no mapa">
                                        <i class="bi bi-geo-alt-fill <?= $corIcone ?>"></i>
                                    </button>
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
<script src="js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

<script>
    const map = L.map('map').setView([-23.5916, -48.0530], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
        attribution: '© OpenStreetMap' 
    }).addTo(map);

    const dadosPontos = <?php echo $pontosJSON; ?>;

    const icones = {
        'inicio': new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41] }),
        'meio':   new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41] }),
        'fim':    new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41] })
    };

    function focarNoPonto(lat, lng, nome) {
        map.flyTo([lat, lng], 17, { animate: true, duration: 1.5 });
        L.popup().setLatLng([lat, lng]).setContent('<b>' + nome + '</b>').openOn(map);
    }

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
                    Resumo do Trajeto: ${(s.totalDistance / 1000).toFixed(2)} km e aprox. ${Math.round(s.totalTime / 60)} min.
                </div>`;
            
            // Ajusta o mapa para caber toda a rota
            const bounds = L.latLngBounds(waypoints);
            map.fitBounds(bounds, {padding: [50, 50]});
        });
    } else if (dadosPontos.length === 1) {
        const p = dadosPontos[0];
        L.marker([p.latitude, p.longitude], { icon: icones['meio'] }).addTo(map).bindPopup(p.nome);
        map.setView([p.latitude, p.longitude], 15);
    }
</script>
</body>
</html>