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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transporte Público - Horários</title>

    <!-- CSS específico -->
    <link rel="stylesheet" href="./css/meu_estilo.css">

    <!-- Fonte local -->
    <link rel="stylesheet" href="./css/fonts.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="./css/bootstrap.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="./css/bootstrap-icons.css">
    
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <style>
        :root {
            --cor-primaria: #1e3c72; 
            --cor-fundo: #f4f7f6;
        }
        body { background-color: var(--cor-fundo); font-family: 'Segoe UI', sans-serif; }
        .nav-pills .nav-link.custom-tab {
            background-color: var(--cor-primaria);
            color: white;
            margin: 0 2px;
            font-weight: 600;
        }
        .nav-pills .nav-link.custom-tab.active {
            background-color: white !important;
            color: var(--cor-primaria) !important;
            border: 1px solid var(--cor-primaria);
        }
        .grid-horarios {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
            gap: 10px;
            padding: 15px 0;
        }
        .horario-btn {
            background: white;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 8px;
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
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 5px;
            color: white;
            min-width: 60px;
            text-align: center;
        }
        .bg-inicio { background-color: #198754; }
        .bg-meio { background-color: #0dcaf0; }
        .bg-fim { background-color: #dc3545; }
        .ponto-item {
            position: relative;
            padding-left: 25px;
            margin-bottom: 12px;
            border-left: 2px dashed #ccc;
        }
        .ponto-item::before {
            content: "";
            position: absolute; left: -7px; top: 6px;
            width: 12px; height: 12px;
            background-color: var(--cor-primaria);
            border-radius: 50%;
        }
    </style>
</head>
<body>

<?php if(file_exists('menu.php')) include 'menu.php'; ?>

<div class="container my-5">
    <?php if ($view == 'lista'): ?>
        <div class="row g-3">
            <?php
            $stmt = $pdo->query("SELECT * FROM tblinhas ORDER BY codigo ASC");
            foreach ($stmt->fetchAll() as $linha):
            ?>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm" style="border-left: 5px solid var(--cor-primaria) !important; cursor:pointer;" onclick="location.href='?view=detalhes&id=<?= $linha['id_linha'] ?>'">
                        <div class="card-body py-3">
                            <span class="badge bg-primary mb-1"><?= $linha['codigo'] ?></span>
                            <h6 class="card-title mb-0 fw-bold"><?= $linha['nome'] ?></h6>
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
                $aba_ativa = 't1'; // Padrão: Dia de semana

                foreach($listaHorarios as $h) {
                    $d = mb_strtolower($h['dia_semana']);
                    if(strpos($d, 'sab') !== false) {
                        $sab[] = $h;
                        if ($id_horario == $h['id_horario']) $aba_ativa = 't2'; // Define aba Sábado se o ID bater
                    } elseif(strpos($d, 'dom') !== false || strpos($d, 'fer') !== false) {
                        $dom[] = $h;
                        if ($id_horario == $h['id_horario']) $aba_ativa = 't3'; // Define aba Domingo se o ID bater
                    } else {
                        $uteis[] = $h;
                        if ($id_horario == $h['id_horario']) $aba_ativa = 't1';
                    }
                }
        ?>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card shadow-sm border-0 p-4 mb-3">
                        <small class="text-muted fw-bold">LINHA <?= $dadosLinha['codigo'] ?></small>
                        <h4 class="text-primary fw-bold text-uppercase mb-4"><?= $dadosLinha['nome'] ?></h4>

                        <ul class="nav nav-pills nav-justified mb-3" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link custom-tab <?= $aba_ativa == 't1' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#t1">Dia de semana</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link custom-tab <?= $aba_ativa == 't2' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#t2">Sábado</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link custom-tab <?= $aba_ativa == 't3' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#t3">Domingo/Feriado</button>
                            </li>
                        </ul>

                        <div class="tab-content border-top pt-2">
                            <?php 
                            $secoes = ['t1' => $uteis, 't2' => $sab, 't3' => $dom];
                            foreach($secoes as $idT => $dados): 
                            ?>
                                <div class="tab-pane fade <?= $aba_ativa == $idT ? 'show active' : '' ?>" id="<?= $idT ?>">
                                    <div class="grid-horarios">
                                        <?php foreach($dados as $h): 
                                            $active = ($id_horario == $h['id_horario']) ? 'active' : '';
                                        ?>
                                            <a href="?view=detalhes&id=<?= $id_linha ?>&id_horario=<?= $h['id_horario'] ?>" class="horario-btn <?= $active ?>">
                                                <?= date('H:i', strtotime($h['horario_partida'])) ?>
                                            </a>
                                        <?php endforeach; ?>
                                     <?php if(empty($dados)) echo "<p class='text-muted fw-bold fs-4 text-nowrap'>Nenhum horário disponível.</p>"; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if ($id_horario > 0): ?>
                        <div class="card shadow-sm border-0 p-4">
                            <h6 class="fw-bold text-secondary mb-4" style="font-size: 0.8rem">ITINERÁRIO DETALHADO</h6>
                            <?php
                            $stmtRotas = $pdo->prepare("SELECT p.nome, r.tipo_ponto, r.horario_previsto FROM tbrotas r JOIN tbpontos p ON r.id_ponto = p.id_ponto WHERE r.id_horario = ? ORDER BY r.ordem ASC");
                            $stmtRotas->execute([$id_horario]);
                            foreach ($stmtRotas->fetchAll() as $ponto):
                                $tipo = mb_strtolower($ponto['tipo_ponto']);
                                $cor = ($tipo == 'inicio') ? 'bg-inicio' : (($tipo == 'fim') ? 'bg-fim' : 'bg-meio');
                            ?>
                                <div class="ponto-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0 fw-bold" style="font-size: 0.9rem;"><?= htmlspecialchars($ponto['nome']) ?></h6>
                                            <small class="text-muted"><?= date('H:i', strtotime($ponto['horario_previsto'])) ?></small>
                                        </div>
                                        <div class="status-badge <?= $cor ?>"><?= $ponto['tipo_ponto'] ?></div>
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

<?php if(file_exists('rodape.php')) include 'rodape.php'; ?>
<!-- Bootstrap JS -->
<script src="./js/bootstrap.bundle.min.js"></script>
</body>
</html>