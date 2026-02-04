<?php
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

// Lógica de navegação simples
$view = isset($_GET['view']) ? $_GET['view'] : 'lista';
$id_linha = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php include 'menu.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transporte Público - Linhas</title>
    
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
    </style>
</head>
<body>

    <header class="header-bg bg-dark">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 fw-bold">Consulta de Itinerários e Pontos</h1>
                   
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
                                <span class="text-primary fw-bold small">VER PONTOS</span>
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
            <!-- VISÃO: PONTOS DA LINHA (ROTAS) -->
            <?php
                // Busca dados da linha
                $stmtLinha = $pdo->prepare("SELECT * FROM tblinhas WHERE id_linha = ?");
                $stmtLinha->execute([$id_linha]);
                $dadosLinha = $stmtLinha->fetch();

                if ($dadosLinha):
            ?>
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white py-3">
                                <h4 class="mb-0">
                                    Linha <?= htmlspecialchars($dadosLinha['codigo']) ?> - 
                                    <span class="text-muted"><?= htmlspecialchars($dadosLinha['nome']) ?></span>
                                </h4>
                            </div>
                            <div class="card-body p-4">
                                <h6 class="text-uppercase text-secondary fw-bold mb-4">Itinerário (Sequência de Pontos)</h6>
                                
                                <div class="itinerario-container">
                                    <?php
                                    // Query para buscar os pontos através da tabela tbrotas
                                    $sqlRotas = "SELECT p.*, r.ordem 
                                                 FROM tbrotas r 
                                                 JOIN tbpontos p ON r.id_ponto = p.id_ponto 
                                                 WHERE r.id_linha = ? 
                                                 ORDER BY r.ordem ASC";
                                    $stmtRotas = $pdo->prepare($sqlRotas);
                                    $stmtRotas->execute([$id_linha]);
                                    $pontos = $stmtRotas->fetchAll();

                                    if (count($pontos) > 0):
                                        foreach ($pontos as $ponto):
                                    ?>
                                        <div class="ponto-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1 fw-bold"><?= htmlspecialchars($ponto['nome']) ?></h6>
                                                    <small class="text-muted d-block">
                                                        Lat: <?= $ponto['latitude'] ?> | Long: <?= $ponto['longitude'] ?>
                                                    </small>
                                                </div>
                                                <span class="badge rounded-pill bg-<?= $ponto['tipo_ponto'] == 'inicio' ? 'success' : ($ponto['tipo_ponto'] == 'fim' ? 'danger' : 'info') ?> small">
                                                    <?= ucfirst($ponto['tipo_ponto']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php 
                                        endforeach;
                                    else:
                                    ?>
                                        <div class="alert alert-light border text-center">
                                            Não há pontos de parada cadastrados para esta rota ainda.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">Linha não encontrada.</div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
<?php include 'rodape.php'; ?>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>