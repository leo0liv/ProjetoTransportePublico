<?php
session_start(); // PASSO 1: Sempre a primeira linha!

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

// Lógica de navegação e variáveis
$view = isset($_GET['view']) ? $_GET['view'] : 'lista';
$id_linha = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Buscamos os dados da linha para o título ANTES do HTML começar
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
    
        <!-- CSS específico -->
    <link rel="stylesheet" href="./css/meu_estilo.css">

    <!-- Fonte local -->
    <link rel="stylesheet" href="./css/fonts.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="./css/bootstrap.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="./css/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    
</head>
<body class="d-flex flex-column min-vh-100">

    <?php include 'menu.php'; ?>

    <header class="header-bg bg-dark">
        <div class="container">
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
                            <?php if ($view == 'lista'): ?>
                                Resultado por buscar: "<strong><?= htmlspecialchars($busca) ?></strong>"
                            <?php else: ?>
                                <span class="text-warning-50">Busca: <?= htmlspecialchars($busca) ?></span>
                            <?php endif; ?>
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

    <main class="container mb-5 flex-grow-1">
        
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
                        <div class="card h-100 card-linha shadow-sm" onclick="location.href='?view=detalhes&id=<?= $linha['id_linha'] ?>'">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="badge-codigo me-3"><?= htmlspecialchars($linha['codigo']) ?></div>
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
                            <h4 class="mb-0">Pontos de Parada</h4>
                        </div>
                        <div class="card-body p-4">
                            <?php
                            $sqlRotas = "SELECT p.*, r.ordem FROM tbrotas r JOIN tbpontos p ON r.id_ponto = p.id_ponto WHERE r.id_linha = ? ORDER BY r.ordem ASC";
                            $stmtRotas = $pdo->prepare($sqlRotas);
                            $stmtRotas->execute([$id_linha]);
                            $pontos = $stmtRotas->fetchAll();

                            if (count($pontos) > 0):
                                foreach ($pontos as $ponto):
                            ?>
                                <div class="ponto-item">
                                    <h6 class="mb-1 fw-bold"><?= htmlspecialchars($ponto['nome']) ?></h6>
                                    <span class="badge rounded-pill bg-<?= $ponto['tipo_ponto'] == 'inicio' ? 'success' : ($ponto['tipo_ponto'] == 'fim' ? 'danger' : 'info') ?> small">
                                        <?= ucfirst($ponto['tipo_ponto']) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-center">Sem pontos cadastrados para esta rota.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'rodape.php'; ?>

    <!-- Bootstrap JS -->
    <script src="./js/bootstrap.bundle.min.js"></script>
</body>
</html>