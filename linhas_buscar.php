<?php
session_start(); 

/**
 * CONFIGURAÇÃO DE CONEXÃO E LÓGICA DO SISTEMA
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

$dadosLinha = null;
if ($view == 'detalhes' && $id_linha > 0) {
    $stmtLinha = $pdo->prepare("SELECT * FROM tblinhas WHERE id_linha = ?");
    $stmtLinha->execute([$id_linha]);
    $dadosLinha = $stmtLinha->fetch();
}
?>

<!DOCTYPE html>
<html lang="pt-br" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transporte Público - Linhas</title>
    <link rel="stylesheet" href="./css/meu_estilo.css">
    <link rel="stylesheet" href="./css/fonts.css">
    <link rel="stylesheet" href="./css/bootstrap.css">
    <link rel="stylesheet" href="./css/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

    <?php include 'menu.php'; ?>

    <header class="header-bg bg-dark">
        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 fw-bold text-white">
                        <?php 
                            if ($view == 'detalhes' && $dadosLinha) {
                                echo 'Itinerário da Linha <span class="text-warning">' . htmlspecialchars($dadosLinha['codigo']) . '</span>';
                            } elseif (!empty($busca)) {
                                echo 'Resultado da Busca';
                            } else {
                                echo 'Todas as Linhas';
                            }
                        ?>
                    </h1>
                    <?php if (!empty($busca)): ?>
                        <p class="lead text-warning mb-0">
                            Busca: "<strong><?= htmlspecialchars($busca) ?></strong>"
                        </p>
                    <?php endif; ?>
                </div>

                <?php if ($view == 'detalhes'): ?>
                    <a href="?view=lista" class="btn btn-outline-light d-flex align-items-center">
                        <span class="material-icons me-1">arrow_back</span> Voltar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="container mb-5 flex-grow-1 mt-4">
        
        <?php if ($view == 'lista'): ?>
            <div class="row g-4">
                <?php
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
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 card-linha shadow-sm" style="cursor:pointer" onclick="location.href='?view=detalhes&id=<?= $linha['id_linha'] ?>'">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="badge bg-primary me-3 p-2"><?= htmlspecialchars($linha['codigo']) ?></div>
                                    <h5 class="card-title mb-0"><?= htmlspecialchars($linha['nome']) ?></h5>
                                </div>
                                <p class="text-muted mb-0 d-flex align-items-center">
                                    <span class="material-icons size-sm me-1" style="font-size: 18px;">business</span>
                                    <?= htmlspecialchars($linha['operadora'] ?: 'Não informada') ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <p class="lead">Nenhuma linha encontrada.</p>
                        <a href="index.php" class="btn btn-primary">Ver todas</a>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($view == 'detalhes' && $id_linha > 0): ?>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3">
                            <h4 class="mb-0">Pontos de Parada e Horários</h4>
                        </div>
                        <div class="card-body p-4">
                            <?php
                            // NOVA CONSULTA: Liga Linha -> Horário Programado -> Rotas -> Pontos
                            $sqlRotas = "SELECT 
                                            p.nome, 
                                            r.ordem, 
                                            r.horario_previsto, 
                                            r.tipo_ponto,
                                            hp.dia_semana,
                                            hp.horario_partida
                                         FROM tbrotas r 
                                         JOIN tbpontos p ON r.id_ponto = p.id_ponto 
                                         JOIN tbhorario_programados hp ON r.id_horario = hp.id_horario
                                         WHERE hp.id_linha = ? 
                                         ORDER BY hp.horario_partida ASC, r.ordem ASC";
                            
                            $stmtRotas = $pdo->prepare($sqlRotas);
                            $stmtRotas->execute([$id_linha]);
                            $pontos = $stmtRotas->fetchAll();

                            if (count($pontos) > 0):
                                $ultimo_horario = '';
                                foreach ($pontos as $ponto):
                                    // Separador visual caso a linha tenha múltiplos horários de saída
                                    if ($ultimo_horario != $ponto['horario_partida']) {
                                        echo "<hr><h5 class='text-primary'>Saída: {$ponto['horario_partida']} ({$ponto['dia_semana']})</h5>";
                                        $ultimo_horario = $ponto['horario_partida'];
                                    }
                            ?>
                                <div class="ponto-item d-flex justify-content-between align-items-center border-bottom py-2">
                                    <div>
                                        <h6 class="mb-0 fw-bold"><?= htmlspecialchars($ponto['nome']) ?></h6>
                                        <small class="text-muted">Ordem: <?= $ponto['ordem'] ?> | Previsto: <?= $ponto['horario_previsto'] ?: '--:--' ?></small>
                                    </div>
                                    <span class="badge rounded-pill bg-<?= $ponto['tipo_ponto'] == 'inicio' ? 'success' : ($ponto['tipo_ponto'] == 'fim' ? 'danger' : 'info') ?>">
                                        <?= ucfirst($ponto['tipo_ponto']) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-center">Sem itinerários ou horários cadastrados para esta linha.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'rodape.php'; ?>

    <script src="./js/bootstrap.bundle.min.js"></script>
</body>
</html>