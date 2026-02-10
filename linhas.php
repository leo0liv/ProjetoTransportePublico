<?php
/**
 * CONFIGURAÇÃO DE CONEXÃO E LÓGICA DO SISTEMA INTEGRADA COM SQL ATUALIZADO
 */
$host = 'localhost';
$db   = 'TransportePublico_ti19';
$user = 'TransportePublico_ti19';
$pass = 'senacti19';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

// Lógica de navegação
$view = isset($_GET['view']) ? $_GET['view'] : 'lista';
$id_linha = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// No novo modelo, podemos querer filtrar por um horário específico da linha
$id_horario = isset($_GET['id_horario']) ? (int)$_GET['id_horario'] : 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php if(file_exists('menu.php')) include 'menu.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transporte Público - Linhas e Horários</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <style>
        body { 
            background-color: #f4f7f6; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .header-bg {
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .card-linha {
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            border: none;
            border-left: 5px solid #1e3c72;
        }
        .card-linha:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .ponto-item {
            position: relative;
            padding-left: 30px;
            margin-bottom: 20px;
            border-left: 2px dashed #adb5bd;
        }
        .ponto-item::before {
            content: "";
            position: absolute;
            left: -9px;
            top: 0;
            width: 16px;
            height: 16px;
            background-color: #1e3c72;
            border-radius: 50%;
            border: 3px solid white;
        }
        .ponto-item:last-child {
            border-left-color: transparent;
        }
        .badge-codigo {
            background-color: #1e3c72;
            color: white;
            font-weight: bold;
            padding: 8px 12px;
            border-radius: 8px;
        }
        .horario-chip {
            background: #e9ecef;
            border-radius: 20px;
            padding: 5px 15px;
            display: inline-block;
            margin-right: 5px;
            margin-bottom: 5px;
            text-decoration: none;
            color: #495057;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        .horario-chip:hover {
            background: #1e3c72;
            color: white;
        }
        .horario-chip.active {
            background: #1e3c72;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <header class="header-bg bg-dark">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 fw-bold">Consulta de Itinerários</h1>
                    <p class="mb-0 text-white-50">Linhas, Horários e Pontos de Parada</p>
                </div>
                <?php if ($view == 'detalhes'): ?>
                    <a href="linhas.php" class="btn btn-outline-light d-flex align-items-center">
                        <span class="material-icons me-1">arrow_back</span> Voltar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="container mb-5">
        
        <?php if ($view == 'lista'): ?>
            <!-- VISÃO: LISTA DE TODAS AS LINHAS -->
            <div class="row g-4">
                <?php
                $stmt = $pdo->query("SELECT * FROM tblinhas ORDER BY codigo ASC");
                $linhas = $stmt->fetchAll();

                if (count($linhas) > 0):
                    foreach ($linhas as $linha):
                ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 card-linha shadow-sm" onclick="location.href='?view=detalhes&id=<?= $linha['id_linha'] ?>'">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="badge-codigo me-3"><?= htmlspecialchars($linha['codigo']) ?></div>
                                    <h5 class="card-title mb-0"><?= htmlspecialchars($linha['nome']) ?></h5>
                                </div>
                                <p class="text-muted mb-0 d-flex align-items-center">
                                    <span class="material-icons size-sm me-1" style="font-size: 18px;">business</span>
                                    <?= htmlspecialchars($linha['operadora'] ?: 'Operadora não informada') ?>
                                </p>
                            </div>
                            <div class="card-footer bg-transparent border-0 text-end">
                                <span class="text-primary fw-bold small">VER HORÁRIOS E PONTOS</span>
                            </div>
                        </div>
                    </div>
                <?php 
                    endforeach; 
                else:
                ?>
                    <div class="col-12 text-center py-5">
                        <span class="material-icons display-1 text-muted">directions_bus</span>
                        <p class="lead mt-3">Nenhuma linha cadastrada no banco de dados.</p>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($view == 'detalhes' && $id_linha > 0): ?>
            <!-- VISÃO: DETALHES DA LINHA (HORÁRIOS E ROTAS) -->
            <?php
                $stmtLinha = $pdo->prepare("SELECT * FROM tblinhas WHERE id_linha = ?");
                $stmtLinha->execute([$id_linha]);
                $dadosLinha = $stmtLinha->fetch();

                if ($dadosLinha):
                    // Busca todos os horários de partida disponíveis para esta linha
                    $stmtH = $pdo->prepare("SELECT * FROM tbhorario_programados WHERE id_linha = ? ORDER BY horario_partida ASC");
                    $stmtH->execute([$id_linha]);
                    $listaHorarios = $stmtH->fetchAll();
                    
                    // Se não houver horário selecionado via GET, mas houver horários na tabela, seleciona o primeiro
                    if ($id_horario == 0 && count($listaHorarios) > 0) {
                        $id_horario = $listaHorarios[0]['id_horario'];
                    }
            ?>
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white py-3">
                                <div class="d-flex align-items-center">
                                    <span class="badge-codigo me-3"><?= htmlspecialchars($dadosLinha['codigo']) ?></span>
                                    <h4 class="mb-0"><?= htmlspecialchars($dadosLinha['nome']) ?></h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <h6 class="text-uppercase text-secondary fw-bold mb-3" style="font-size: 0.8rem;">Selecione um Horário de Saída:</h6>
                                <div class="mb-2">
                                    <?php if (count($listaHorarios) > 0): ?>
                                        <?php foreach ($listaHorarios as $h): ?>
                                            <a href="?view=detalhes&id=<?= $id_linha ?>&id_horario=<?= $h['id_horario'] ?>" 
                                               class="horario-chip <?= $id_horario == $h['id_horario'] ? 'active' : '' ?>">
                                               <?= date('H:i', strtotime($h['horario_partida'])) ?> 
                                               <small>(<?= htmlspecialchars($h['dia_semana']) ?>)</small>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted small">Nenhum horário programado para esta linha.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <?php if ($id_horario > 0): ?>
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="text-uppercase text-secondary fw-bold mb-0">Itinerário Detalhado</h6>
                                    <span class="badge bg-light text-dark border">
                                        Partida: 
                                        <?php 
                                            foreach($listaHorarios as $lh) {
                                                if($lh['id_horario'] == $id_horario) echo date('H:i', strtotime($lh['horario_partida']));
                                            }
                                        ?>
                                    </span>
                                </div>
                                
                                <div class="itinerario-container">
                                    <?php
                                    // NOVO SQL: Busca os pontos vinculados ao ID_HORARIO específico na tabela tbrotas
                                    $sqlRotas = "SELECT p.*, r.ordem, r.horario_previsto, r.tipo_ponto 
                                                 FROM tbrotas r 
                                                 JOIN tbpontos p ON r.id_ponto = p.id_ponto 
                                                 WHERE r.id_horario = ? 
                                                 ORDER BY r.ordem ASC";
                                    $stmtRotas = $pdo->prepare($sqlRotas);
                                    $stmtRotas->execute([$id_horario]);
                                    $pontos = $stmtRotas->fetchAll();

                                    if (count($pontos) > 0):
                                        foreach ($pontos as $ponto):
                                            // Define cores baseadas no tipo de ponto do novo SQL
                                            $corBadge = 'info';
                                            if ($ponto['tipo_ponto'] == 'inicio') $corBadge = 'success';
                                            if ($ponto['tipo_ponto'] == 'fim') $corBadge = 'danger';
                                    ?>
                                        <div class="ponto-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="d-flex align-items-center">
                                                        <h6 class="mb-1 fw-bold"><?= htmlspecialchars($ponto['nome']) ?></h6>
                                                        <?php if ($ponto['horario_previsto']): ?>
                                                            <span class="ms-2 badge bg-dark opacity-75" style="font-size: 0.7rem;">
                                                                Previsto: <?= date('H:i', strtotime($ponto['horario_previsto'])) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <small class="text-muted d-block">
                                                        Latitude: <?= $ponto['latitude'] ?> | Longitude: <?= $ponto['longitude'] ?>
                                                    </small>
                                                </div>
                                                <span class="badge rounded-pill bg-<?= $corBadge ?> small">
                                                    <?= ucfirst($ponto['tipo_ponto']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php 
                                        endforeach;
                                    else:
                                    ?>
                                        <div class="alert alert-light border text-center py-4">
                                            <span class="material-icons text-muted">map</span>
                                            <p class="mb-0 mt-2">Não há pontos de parada vinculados a este horário.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">Linha não encontrada.</div>
            <?php endif; ?>
        <?php endif; ?>

    </div>

    <?php if(file_exists('rodape.php')) include 'rodape.php'; ?>
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>