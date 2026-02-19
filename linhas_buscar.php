<?php
session_start(); 

/**
 * CONFIGURAÇÃO DE CONEXÃO
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

// LÓGICA DE PERSISTÊNCIA DA BUSCA
if (isset($_GET['buscar'])) {
    $_SESSION['ultima_busca'] = $_GET['buscar'];
    $busca = $_GET['buscar'];
} elseif (isset($_SESSION['ultima_busca'])) {
    $busca = $_SESSION['ultima_busca'];
} else {
    $busca = '';
}

$view = isset($_GET['view']) ? $_GET['view'] : 'lista';
$id_linha = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$id_horario = isset($_GET['id_horario']) ? (int)$_GET['id_horario'] : 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transporte Público - Linhas</title>
    
    <link rel="stylesheet" href="./css/meu_estilo.css">
    <link rel="stylesheet" href="./css/bootstrap.css">
    <link rel="stylesheet" href="./css/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <style>
        :root {
            --cor-primaria: #1e3c72; 
            --cor-fundo: #f8f9fa;
            --cor-dark: #212529;
            --cor-azul-claro: #3b82f6;
        }

        body { background-color: var(--cor-fundo); font-family: 'Segoe UI', sans-serif; }

        /* HEADER ESTILO DARK (Conforme imagem_f62903.png) */
        .header-bg-premium { background-color: var(--cor-dark); color: white; padding: 40px 0; }
        
        /* CARDS DE LINHA (Conforme image_f62c2b.png) */
        .card-linha {
            border: none;
            border-left: 5px solid var(--cor-azul-claro) !important;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .card-linha:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }

        /* ABAS DE HORÁRIO (Conforme image_f68ea1.png) */
        .nav-pills-custom .nav-link {
            background-color: #2c4a7c;
            color: white;
            margin: 0 5px;
            font-weight: 600;
            border-radius: 8px;
            padding: 12px;
        }
        .nav-pills-custom .nav-link.active {
            background-color: white !important;
            color: #2c4a7c !important;
            border: 2px solid #2c4a7c;
        }

        /* GRID DE HORÁRIOS */
        .grid-horarios { display: grid; grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); gap: 10px; }
        .horario-btn {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            text-decoration: none;
            color: #333;
            font-weight: bold;
            transition: 0.2s;
        }
        .horario-btn:hover { border-color: var(--cor-azul-claro); background: #f0f7ff; }

        /* TIMELINE (Conforme image_f68e05.png) */
        .timeline { position: relative; padding-left: 20px; }
        .timeline::before {
            content: ''; position: absolute; top: 10px; bottom: 0; left: 5px;
            width: 2px; background: #dee2e6; border-left: 2px dashed #adb5bd;
        }
        .timeline-item { position: relative; margin-bottom: 25px; padding-left: 20px; }
        .timeline-item::before {
            content: ''; position: absolute; left: -20px; top: 6px;
            width: 12px; height: 12px; border-radius: 50%;
            background-color: var(--cor-primaria); border: 2px solid white;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <?php include 'menu.php'; ?>

    <header class="header-bg-premium">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 fw-bold">
                        <?php 
                            if ($view == 'detalhes' && $id_linha > 0) echo "Itinerário da Linha";
                            elseif (!empty($busca)) echo "Resultado da Busca";
                            else echo "Linhas Disponíveis";
                        ?>
                    </h1>
                    <?php if (!empty($busca)): ?>
                        <p class="lead text-warning mb-0">Busca: "<strong><?= htmlspecialchars($busca) ?></strong>"</p>
                    <?php endif; ?>
                </div>

                <?php if ($view == 'detalhes'): ?>
                    <a href="?view=lista" class="btn btn-outline-light rounded-pill px-4">
                        <i class="bi bi-arrow-left me-2"></i>Voltar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="container py-5 flex-grow-1">
        
        <?php if ($view == 'lista'): ?>
            <div class="row g-4">
                <?php
                // Correção do erro de SQL (image_f69544.png)
                if ($busca !== '') {
                    $sql = "SELECT * FROM tblinhas WHERE nome LIKE :t1 OR codigo LIKE :t2 ORDER BY codigo ASC";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['t1' => '%' . $busca . '%', 't2' => '%' . $busca . '%']);
                } else {
                    $stmt = $pdo->query("SELECT * FROM tblinhas ORDER BY codigo ASC");
                }
                
                $linhas = $stmt->fetchAll();

                if (count($linhas) > 0):
                    foreach ($linhas as $linha):
                ?>
                    <div class="col-md-4">
                        <div class="card h-100 card-linha shadow-sm" style="cursor:pointer" onclick="location.href='?view=detalhes&id=<?= $linha['id_linha'] ?>'">
                            <div class="card-body">
                                <span class="badge bg-primary mb-2"><?= htmlspecialchars($linha['codigo']) ?></span>
                                <h5 class="card-title fw-bold mb-0 text-dark"><?= htmlspecialchars($linha['nome']) ?></h5>
                            </div>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                    <div class="col-12 text-center py-5">
                        <p class="lead">Nenhuma linha encontrada para "<?= htmlspecialchars($busca) ?>".</p>
                        <a href="index.php" class="btn btn-primary">Ver todas as linhas</a>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($view == 'detalhes' && $id_linha > 0): 
            $stmtL = $pdo->prepare("SELECT * FROM tblinhas WHERE id_linha = ?");
            $stmtL->execute([$id_linha]);
            $dadosLinha = $stmtL->fetch();
        ?>
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="card shadow-sm border-0 p-4 mb-4" style="border-radius: 12px;">
                        <h6 class="text-muted fw-bold mb-1">LINHA <?= $dadosLinha['codigo'] ?></h6>
                        <h2 class="text-primary fw-bold mb-4"><?= mb_strtoupper($dadosLinha['nome']) ?></h2>

                        <?php
                        $stmtH = $pdo->prepare("SELECT * FROM tbhorario_programados WHERE id_linha = ? ORDER BY horario_partida ASC");
                        $stmtH->execute([$id_linha]);
                        $todosHorarios = $stmtH->fetchAll();

                        // Separa horários por categoria
                        $uteis = []; $sabados = []; $domingos = [];
                        foreach($todosHorarios as $h) {
                            $dia = mb_strtolower($h['dia_semana']);
                            if(strpos($dia, 'sab') !== false) $sabados[] = $h;
                            elseif(strpos($dia, 'dom') !== false || strpos($dia, 'fer') !== false) $domingos[] = $h;
                            else $uteis[] = $h;
                        }
                        ?>

                        <ul class="nav nav-pills nav-justified nav-pills-custom mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#dia-util">Dia de semana</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#sabado">Sábado</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#domingo">Domingo/Feriado</button></li>
                        </ul>

                        <div class="tab-content border-top pt-4">
                            <?php 
                            $categorias = ['dia-util' => $uteis, 'sabado' => $sabados, 'domingo' => $domingos];
                            foreach($categorias as $idCat => $listaH): 
                            ?>
                                <div class="tab-pane fade <?= $idCat == 'dia-util' ? 'show active' : '' ?>" id="<?= $idCat ?>">
                                    <div class="grid-horarios">
                                        <?php foreach($listaH as $h): ?>
                                            <a href="?view=detalhes&id=<?= $id_linha ?>&id_horario=<?= $h['id_horario'] ?>" 
                                               class="horario-btn <?= $id_horario == $h['id_horario'] ? 'border-primary bg-light text-primary' : '' ?>">
                                                <?= date('H:i', strtotime($h['horario_partida'])) ?>
                                            </a>
                                        <?php endforeach; if(empty($listaH)) echo "<p class='text-muted'>Sem horários registrados.</p>"; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if ($id_horario > 0): ?>
                        <div class="card shadow-sm border-0 p-4 p-md-5" style="border-radius: 12px;">
                            <h5 class="fw-bold mb-4"><i class="bi bi-geo-alt-fill text-primary me-2"></i>Pontos de Parada e Horários</h5>
                            <div class="timeline">
                                <?php
                                $stmtRotas = $pdo->prepare("SELECT p.nome, r.ordem, r.horario_previsto, r.tipo_ponto 
                                                          FROM tbrotas r 
                                                          JOIN tbpontos p ON r.id_ponto = p.id_ponto 
                                                          WHERE r.id_horario = ? 
                                                          ORDER BY r.ordem ASC");
                                $stmtRotas->execute([$id_horario]);
                                foreach ($stmtRotas->fetchAll() as $ponto):
                                    $corBadge = ($ponto['tipo_ponto'] == 'inicio') ? 'success' : (($ponto['tipo_ponto'] == 'fim') ? 'danger' : 'info');
                                ?>
                                    <div class="timeline-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0 fw-bold"><?= htmlspecialchars($ponto['nome']) ?></h6>
                                            <small class="text-muted">Ordem: <?= $ponto['ordem'] ?> | Previsto: <?= date('H:i', strtotime($ponto['horario_previsto'])) ?></small>
                                        </div>
                                        <span class="badge rounded-pill bg-<?= $corBadge ?>"><?= ucfirst($ponto['tipo_ponto']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'rodape.php'; ?>

    <script src="./js/bootstrap.bundle.min.js"></script>
</body>
</html>