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

$view = isset($_GET['view']) ? $_GET['view'] : 'lista';
$id_linha = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$id_horario = isset($_GET['id_horario']) ? (int)$_GET['id_horario'] : 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php if(file_exists('menu.php')) include 'menu.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transporte Público - Linhas e Horários</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <style>
        :root {
            --cor-primaria: #1e3c72; 
            --cor-fundo: #f4f7f6;
        }

        body { 
            background-color: var(--cor-fundo); 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header-bg {
            background-color: #212529;
            color: white;
            padding: 20px 0;
            margin-bottom: 20px;
        }

        /* BARRA DE PESQUISA */
        .search-container {
            background: white;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .search-input {
            border: 2px solid #eee;
            border-radius: 8px;
            padding: 10px 15px 10px 45px;
            font-size: 1rem;
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        /* HORÁRIOS E ABAS */
        .nav-pills .nav-link.custom-tab {
            background-color: var(--cor-primaria);
            color: white;
            margin: 0 4px;
            border: 2px solid var(--cor-primaria);
            font-weight: 600;
        }

        .nav-pills .nav-link.custom-tab.active {
            background-color: white !important;
            color: var(--cor-primaria) !important;
        }

        .grid-horarios {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            padding: 20px 0;
        }

        .horario-btn {
            display: block;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }

        .horario-btn.active {
            background-color: #eef2f7;
            border-color: var(--cor-primaria);
            color: var(--cor-primaria);
        }

        /* BADGES DO ITINERÁRIO */
        .status-badge {
            display: inline-block;
            padding: 2px 10px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 4px;
            color: white;
            min-width: 55px;
            text-align: center;
        }
        .bg-inicio { background-color: #198754; }
        .bg-meio { background-color: #0dcaf0; }
        .bg-fim { background-color: #dc3545; }

        .ponto-item {
            position: relative;
            padding-left: 30px;
            margin-bottom: 15px;
            border-left: 2px dashed #adb5bd;
        }

        footer { padding: 10px 0; font-size: 0.8rem; }
    </style>
</head>
<body>

<header class="header-bg">
    <div class="container d-flex justify-content-between align-items-center">
        <h1 class="h4 fw-bold mb-0">Transporte Coletivo</h1>
        <?php if ($view == 'detalhes'): ?>
            <a href="linhas.php" class="btn btn-sm btn-outline-light">Voltar para a Lista</a>
        <?php endif; ?>
    </div>
</header>

<div class="container mb-5">

    <?php if ($view == 'lista'): ?>
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="search-container position-relative">
                    <span class="material-icons search-icon">search</span>
                    <input type="text" id="inputPesquisa" class="form-control search-input" placeholder="Pesquisar por nome ou código da linha...">
                </div>
            </div>
        </div>

        <div class="row g-4" id="listaLinhas">
            <?php
            $stmt = $pdo->query("SELECT * FROM tblinhas ORDER BY codigo ASC");
            foreach ($stmt->fetchAll() as $linha):
            ?>
                <div class="col-md-4 card-linha-container" data-busca="<?= mb_strtolower($linha['codigo'] . ' ' . $linha['nome']) ?>">
                    <div class="card h-100 border-0 shadow-sm" style="border-left: 5px solid #1e3c72 !important; cursor:pointer;" onclick="location.href='?view=detalhes&id=<?= $linha['id_linha'] ?>'">
                        <div class="card-body">
                            <span class="badge bg-primary mb-2"><?= $linha['codigo'] ?></span>
                            <h5 class="card-title fw-bold"><?= $linha['nome'] ?></h5>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php elseif ($view == 'detalhes' && $id_linha > 0): ?>
        <?php
            $stmtLinha = $pdo->prepare("SELECT * FROM tblinhas WHERE id_linha = ?");
            $stmtLinha->execute([$id_linha]);
            $dadosLinha = $stmtLinha->fetch();

            if ($dadosLinha):
                $stmtH = $pdo->prepare("SELECT * FROM tbhorario_programados WHERE id_linha = ? ORDER BY horario_partida ASC");
                $stmtH->execute([$id_linha]);
                $listaHorarios = $stmtH->fetchAll();

                $uteis = []; $sab = []; $dom = [];
                foreach($listaHorarios as $h) {
                    $d = mb_strtolower($h['dia_semana']);
                    if(strpos($d, 'sab') !== false) $sab[] = $h;
                    elseif(strpos($d, 'dom') !== false || strpos($d, 'fer') !== false) $dom[] = $h;
                    else $uteis[] = $h;
                }
        ?>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card shadow-sm border-0 p-4 mb-4">
                        <h6 class="fw-bold text-muted mb-1">LINHA <?= $dadosLinha['codigo'] ?></h6>
                        <h4 class="text-primary fw-bold text-uppercase mb-4"><?= $dadosLinha['nome'] ?></h4>

                        <ul class="nav nav-pills nav-justified mb-3" role="tablist">
                            <li class="nav-item"><button class="nav-link custom-tab active" data-bs-toggle="pill" data-bs-target="#tab-uteis">Dia de semana</button></li>
                            <li class="nav-item"><button class="nav-link custom-tab" data-bs-toggle="pill" data-bs-target="#tab-sab">Sábado</button></li>
                            <li class="nav-item"><button class="nav-link custom-tab" data-bs-toggle="pill" data-bs-target="#tab-dom">Domingo/Feriado</button></li>
                        </ul>

                        <div class="tab-content border-top pt-3">
                            <?php 
                            $abas = ['tab-uteis' => $uteis, 'tab-sab' => $sab, 'tab-dom' => $dom];
                            foreach($abas as $idTab => $dados): 
                            ?>
                                <div class="tab-pane fade <?= $idTab == 'tab-uteis' ? 'show active' : '' ?>" id="<?= $idTab ?>">
                                    <div class="grid-horarios">
                                        <?php foreach($dados as $h): 
                                            $active = ($id_horario == $h['id_horario']) ? 'active' : '';
                                        ?>
                                            <a href="?view=detalhes&id=<?= $id_linha ?>&id_horario=<?= $h['id_horario'] ?>" class="horario-btn <?= $active ?>">
                                                <?= date('H:i', strtotime($h['horario_partida'])) ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if ($id_horario > 0): ?>
                        <div class="card shadow-sm border-0 p-4">
                            <h6 class="fw-bold text-uppercase text-secondary mb-4">Itinerário</h6>
                            <?php
                            $stmtRotas = $pdo->prepare("SELECT p.nome, r.tipo_ponto, r.horario_previsto FROM tbrotas r JOIN tbpontos p ON r.id_ponto = p.id_ponto WHERE r.id_horario = ? ORDER BY r.ordem ASC");
                            $stmtRotas->execute([$id_horario]);
                            foreach ($stmtRotas->fetchAll() as $ponto):
                                $tipo = mb_strtolower($ponto['tipo_ponto']);
                                $classeCor = ($tipo == 'inicio') ? 'bg-inicio' : (($tipo == 'fim') ? 'bg-fim' : 'bg-meio');
                            ?>
                                <div class="ponto-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0 fw-bold"><?= $ponto['nome'] ?></h6>
                                            <small class="text-muted"><?= date('H:i', strtotime($ponto['horario_previsto'])) ?></small>
                                        </div>
                                        <div class="status-badge <?= $classeCor ?>"><?= $ponto['tipo_ponto'] ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<footer class="bg-dark text-white text-center py-2 mt-auto">
    <small>&copy; 2026 Transporte Público</small>
</footer>

<script>
document.getElementById('inputPesquisa')?.addEventListener('input', function(e) {
    let termo = e.target.value.toLowerCase();
    let cards = document.querySelectorAll('.card-linha-container');
    
    cards.forEach(card => {
        let textoNoCard = card.getAttribute('data-busca');
        if (textoNoCard.includes(termo)) {
            card.style.display = "block";
        } else {
            card.style.display = "none";
        }
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>