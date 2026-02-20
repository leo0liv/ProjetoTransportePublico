<?php
// CONEXÃO E FUNÇÕES
require_once './connections/db_connect.php';

$view = isset($_GET['view']) ? $_GET['view'] : 'lista';
$id_linha = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$id_horario = isset($_GET['id_horario']) ? (int)$_GET['id_horario'] : 0;

// Termo vindo do menu
$termo_pesquisa = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// Se houver pesquisa, forçamos a lista para mostrar os resultados
if (!empty($termo_pesquisa) && $view !== 'detalhes') {
    $view = 'lista';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transporte Público - Horários</title>

    <link rel="stylesheet" href="./css/meu_estilo.css">
    <link rel="stylesheet" href="./css/fonts.css">
    <link rel="stylesheet" href="./css/bootstrap.css">
    <link rel="stylesheet" href="./css/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <style>
        :root {
            --cor-primaria: #1e3c72;
            --cor-fundo: #f4f7f6;
        }
        body { 
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            background-color: var(--cor-fundo); 
            font-family: 'Segoe UI', sans-serif; 
        }

        .container.my-5 { flex: 1; }
        
        .card-linha {
            transition: transform 0.2s;
            border-left: 5px solid var(--cor-primaria) !important;
            border-radius: 10px;
        }
        .card-linha:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
        }

        .nav-pills .nav-link.custom-tab {
            background-color: var(--cor-primaria);
            color: white;
            font-weight: 600;
            margin: 0 5px; 
            border: 1px solid var(--cor-primaria);
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
        .bg-meio { background-color: #4169E1; }
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
        
        <?php if (!empty($termo_pesquisa)): ?>
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h5><i class="bi bi-search"></i> Resultados para: <strong><?= htmlspecialchars($termo_pesquisa) ?></strong></h5>
                <a href="?view=lista" class="btn btn-outline-secondary btn-sm">Limpar Busca</a>
            </div>
        <?php endif; ?>

        <div class="row g-3">
            <?php
            if (!empty($termo_pesquisa)) {
                $stmt = $pdo->prepare("SELECT * FROM tblinhas WHERE nome LIKE ? OR codigo LIKE ? ORDER BY codigo ASC");
                $stmt->execute(["%$termo_pesquisa%", "%$termo_pesquisa%"]);
            } else {
                $stmt = $pdo->query("SELECT * FROM tblinhas ORDER BY codigo ASC");
            }
            
            $linhas = $stmt->fetchAll();

            if (count($linhas) > 0):
                foreach ($linhas as $linha):
                    // Geramos o link de detalhes passando também o termo buscado
                    $url_detalhes = "?view=detalhes&id=" . $linha['id_linha'];
                    if (!empty($termo_pesquisa)) {
                        $url_detalhes .= "&buscar=" . urlencode($termo_pesquisa);
                    }
            ?>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm card-linha" style="cursor:pointer;" onclick="location.href='<?= $url_detalhes ?>'">
                        <div class="card-body py-3">
                            <span class="badge bg-primary mb-1"><?= $linha['codigo'] ?></span>
                            <h6 class="card-title mb-0 fw-bold"><?= $linha['nome'] ?></h6>
                        </div>
                    </div>
                </div>
            <?php 
                endforeach; 
            else: 
            ?>
                <div class="col-12 text-center py-5">
                    <div class="bg-white p-5 rounded shadow-sm">
                        <i class="bi bi-bus-front text-danger" style="font-size: 4rem; opacity: 0.3;"></i>
                        <h2 class="mt-3 fw-bold text-dark">Linha não encontrada</h2>
                        <p class="text-muted fs-5">Não localizamos nenhuma linha com o termo "<strong><?= htmlspecialchars($termo_pesquisa) ?></strong>".</p>
                        <a href="?view=lista" class="btn btn-primary mt-3 px-4">Ver Todas as Linhas</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    <?php elseif ($view == 'detalhes' && $id_linha > 0): ?>
        <?php
            $stmtLinha = $pdo->prepare("SELECT * FROM tblinhas WHERE id_linha = ?");
            $stmtLinha->execute([$id_linha]);
            $dadosLinha = $stmtLinha->fetch();

            if ($dadosLinha):
                // Lógica de horários
                $stmtH = $pdo->prepare("SELECT * FROM tbhorario_programados WHERE id_linha = ? ORDER BY horario_partida ASC");
                $stmtH->execute([$id_linha]);
                $listaHorarios = $stmtH->fetchAll();

                $uteis = []; $sab = []; $dom = [];
                $aba_ativa = 't1';

                foreach($listaHorarios as $h) {
                    $d = mb_strtolower($h['dia_semana']);
                    if(strpos($d, 'sab') !== false) {
                        $sab[] = $h;
                        if ($id_horario == $h['id_horario']) $aba_ativa = 't2';
                    } elseif(strpos($d, 'dom') !== false || strpos($d, 'fer') !== false) {
                        $dom[] = $h;
                        if ($id_horario == $h['id_horario']) $aba_ativa = 't3';
                    } else {
                        $uteis[] = $h;
                        if ($id_horario == $h['id_horario']) $aba_ativa = 't1';
                    }
                }

                // MONTAR O LINK DE VOLTAR
                // Se existe termo de pesquisa, volta para a lista filtrada, senão volta para a lista geral
                $url_voltar = !empty($termo_pesquisa) ? "?view=lista&buscar=" . urlencode($termo_pesquisa) : "?view=lista";
        ?>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    
                    <div class="mb-3">
                        <a href="<?= $url_voltar ?>" class="btn btn-sm btn-secondary shadow-sm">
                            <i class="bi bi-arrow-left"></i> Voltar para <?= !empty($termo_pesquisa) ? 'a busca' : 'a lista' ?>
                        </a>
                    </div>

                    <div class="card shadow-sm border-0 p-4 mb-3 text-center"> 
                        <small class="text-muted fw-bold">LINHA <?= $dadosLinha['codigo'] ?></small>
                        <h4 class="text-primary fw-bold text-uppercase mb-4"><?= $dadosLinha['nome'] ?></h4>

                        <ul class="nav nav-pills justify-content-center mb-3" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link custom-tab <?= $aba_ativa == 't1' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#t1">Dia de semana</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link custom-tab <?= $aba_ativa == 't2' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#t2">Sábado</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link custom-tab <?= $aba_ativa == 't3' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#t3">Dom/Feriado</button>
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
                                            // Mantemos o termo de busca nos links de horários para não perder a referência
                                            $url_horario = "?view=detalhes&id=$id_linha&id_horario=".$h['id_horario'];
                                            if(!empty($termo_pesquisa)) $url_horario .= "&buscar=".urlencode($termo_pesquisa);
                                        ?>
                                            <a href="<?= $url_horario ?>" class="horario-btn <?= $active ?>">
                                                <?= date('H:i', strtotime($h['horario_partida'])) ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php if(empty($dados)) echo "<p class='text-muted fw-bold py-3'>Nenhum horário disponível.</p>"; ?>
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
                                <div class="ponto-item text-start"> 
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
<script src="./js/bootstrap.bundle.min.js"></script>
</body>
</html>