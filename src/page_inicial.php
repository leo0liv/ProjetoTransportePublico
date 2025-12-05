<?php
// Inclui o arquivo de conexão com o banco de dados
include '../connections/db_connect.php';

// Função utilitária para retornar um valor COUNT(*)
function queryValue($conn, $sql) {
    return $conn->query($sql)->fetch_assoc()['t'] ?? 0;
}

// Estatísticas gerais do sistema
$stats = [
    'linhas'            => queryValue($conn, "SELECT COUNT(*) AS t FROM tblinhas"),
    'veiculos_ativos'   => queryValue($conn, "SELECT COUNT(*) AS t FROM tbveiculos"),
    'pontos'            => queryValue($conn, "SELECT COUNT(*) AS t FROM tbpontos"),
    'motoristas_ativos' => queryValue($conn, "SELECT COUNT(*) AS t FROM tbmotoristas"),
];

// Consulta das últimas localizações registradas em tempo real
$sqlLocal = "SELECT l.*, v.placa, lin.codigo AS linha_codigo
             FROM tblocalizacao_tempo_real l
             JOIN tbveiculos v ON l.id_veiculo = v.id_veiculo
             LEFT JOIN tblinhas lin ON v.id_linha = lin.id_linha
             ORDER BY l.timestamp_atualizacao DESC
             LIMIT 5
";

// Executa a busca e retorna como array associativo
$localizacoes = $conn->query($sqlLocal)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Inicial</title>

    <!-- CSS específico -->
    <link rel="stylesheet" href="../css/meu_estilo.css">

    <!-- Fonte local -->
    <link rel="stylesheet" href="../css/fonts.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../css/bootstrap.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="../css/bootstrap-icons.css">

    <style>
        body { margin: 0; padding: 0; }
        /* Garante que o iframe ocupe a maior parte da tela */
        #map-container {
            width: 100%;
            height: 90vh; /* 90% da altura da tela */
        }
    </style>

</head>

<body>
    <main class="container py-4">
        <h1 class="mb-4">
            <i class="bi bi-speedometer2"></i> Monitoramento em Tempo Real
        </h1>
        
        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-signpost-2 fs-1 text-primary"></i>
                        <h3 class="mt-2 mb-0"><?php echo $stats['linhas']; ?></h3>
                        <p class="text-muted mb-0">Linhas</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-bus-front fs-1 text-success"></i>
                        <h3 class="mt-2 mb-0"><?php echo $stats['veiculos_ativos']; ?></h3>
                        <p class="text-muted mb-0">Veículos Ativos</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-geo-alt fs-1 text-warning"></i>
                        <h3 class="mt-2 mb-0"><?php echo $stats['pontos']; ?></h3>
                        <p class="text-muted mb-0">Pontos</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-person-badge fs-1 text-info"></i>
                        <h3 class="mt-2 mb-0"><?php echo $stats['motoristas_ativos']; ?></h3>
                        <p class="text-muted mb-0">Motoristas Ativos</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Mapa Simulado -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-map"></i> Mapa de Localização
                    </div>
                    <div class="card-body">
                        <div class="map-container d-flex align-items-center justify-content-center">
                            <div class="text-center text-muted">
                                <i class="bi bi-map fs-1"></i>
                                <div id="controls">
                                    Exibindo Rota da Linha ID: 1 (Terminal - Vila Rio Branco)
                                </div>
                                <iframe id="map-iframe" src="" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Últimas Localizações -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-clock-history"></i> Últimas Atualizações
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php if (empty($localizacoes)): ?>
                                <li class="list-group-item text-center text-muted py-4">
                                    Nenhuma localização registrada.
                                </li>
                            <?php else: ?>
                                <?php foreach ($localizacoes as $loc): ?>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><?php echo $loc['placa']; ?></strong>
                                                <?php if ($loc['linha_codigo']): ?>
                                                    <span class="badge bg-primary ms-1"><?php echo $loc['linha_codigo']; ?></span>
                                                <?php endif; ?>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo number_format($loc['velocidade'], 1); ?> km/h
                                                </small>
                                            </div>
                                            <small class="text-muted">
                                                <?php echo date('H:i', strtotime($loc['timestamp_atualizacao'])); ?>
                                            </small>
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
    <script src="../js/bootstrap.bundle.min.js"></script>
    

</body>

</html>