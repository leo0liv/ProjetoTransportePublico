<?php
/**
 * Sistema Integrado: Login e Monitoramento Operacional
 * PHP 8.2.12 | Bootstrap 5.3 | Leaflet JS
 */

session_start();

// --- 1. CONFIGURAÇÃO DE CONEXÃO (Simulação/Integração) ---
// Em um cenário real, você usaria: include("../connections/db_connect.php");
// Aqui definimos os dados de acesso para exemplo
$logado = isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true;

// --- 2. LÓGICA DE AUTENTICAÇÃO ---
$erro_login = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_login'])) {
    $usuario_form = $_POST['usuario'];
    $senha_form = $_POST['senha'];

    // Credenciais de exemplo (Admin padrão)
    if ($usuario_form === "admin" && $senha_form === "1234") {
        $_SESSION['admin_logado'] = true;
        $_SESSION['admin_nome'] = "Administrador Sistema";
        header("Location: " . $_SERVER['PHP_SELF']); // Recarrega a página logado
        exit();
    } else {
        $erro_login = "Usuário ou senha inválidos.";
    }
}

// --- 3. LOGOUT ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// --- 4. BUSCA DE DADOS (Simulado para o Canvas) ---
// Se estivesse conectado ao banco:
// $res_motoristas = $conn->query("SELECT id_motorista, nome FROM tbmotoristas ORDER BY nome ASC");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área Operacional - Sistema de Transportes</title>
    
    <!-- CSS: Bootstrap 5.3, Icons e Leaflet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body { background-color: #f4f7f9; font-family: 'Inter', sans-serif; overflow-x: hidden; }
        
        /* Estilos da Tela de Login */
        .login-wrapper {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        }
        .login-card {
            background: white;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }

        /* Estilos do Monitoramento */
        .layout-monitor {
            display: grid;
            grid-template-columns: 1fr 350px;
            height: calc(100vh - 80px);
            gap: 20px;
            padding: 20px;
        }
        @media (max-width: 992px) {
            .layout-monitor { grid-template-columns: 1fr; height: auto; }
            #map-canvas { height: 400px; }
        }
        #map-canvas {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid #e0e6ed;
            z-index: 1;
        }
        .side-panel {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 20px;
            display: flex;
            flex-direction: column;
            border: 1px solid #e0e6ed;
        }
        .pulse-online {
            width: 10px; height: 10px;
            background: #22c55e;
            border-radius: 50%;
            display: inline-block;
            animation: pulse-gps 2s infinite;
        }
        @keyframes pulse-gps {
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }
        .bus-marker {
            background: #2563eb; color: white;
            border-radius: 50%; border: 2px solid white;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>

<?php if (!$logado): ?>
    <!-- TELA DE LOGIN -->
    <div class="login-wrapper">
        <div class="login-card">
            <div class="text-center mb-4">
                <i class="bi bi-bus-front-fill fs-1 text-primary"></i>
                <h3 class="fw-bold mt-2">Área Operacional</h3>
                <p class="text-muted small">Entre com suas credenciais de acesso</p>
            </div>

            <?php if ($erro_login): ?>
                <div class="alert alert-danger py-2 small"><?= $erro_login ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">USUÁRIO</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                        <input type="text" name="usuario" class="form-control border-start-0 bg-light" placeholder="admin" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold">SENHA</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-key"></i></span>
                        <input type="password" name="senha" class="form-control border-start-0 bg-light" placeholder="1234" required>
                    </div>
                </div>
                <button type="submit" name="btn_login" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                    ACESSAR MONITORAMENTO
                </button>
            </form>
            <div class="text-center mt-4 text-white-50 small">
                © 2024 Gestão Municipal de Transportes
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- ÁREA DE MONITORAMENTO (LOGADO) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm px-4">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#"><i class="bi bi-geo-alt-fill me-2 text-primary"></i>OPS MONITOR</a>
            <div class="d-flex align-items-center">
                <span class="text-light me-3 small d-none d-md-inline">Bem-vindo, <b><?= $_SESSION['admin_nome'] ?></b></span>
                <a href="?logout=1" class="btn btn-outline-danger btn-sm fw-bold">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid pt-3">
        <div class="d-flex justify-content-between align-items-center px-3 mb-2">
            <div>
                <h4 class="fw-bold text-dark mb-0">Monitoramento em Tempo Real</h4>
                <span class="text-muted small">Gerenciamento de turnos e geolocalização da frota</span>
            </div>
            <div class="d-flex align-items-center bg-white border rounded-pill px-3 py-1 shadow-sm">
                <span class="pulse-online me-2"></span>
                <span class="text-success fw-bold" style="font-size: 0.75rem;">SISTEMA ONLINE</span>
            </div>
        </div>

        <div class="layout-monitor">
            <!-- Mapa -->
            <div id="map-canvas"></div>

            <!-- Painel Lateral -->
            <aside class="side-panel">
                <div class="mb-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">
                        <i class="bi bi-calendar-plus me-2 text-primary"></i>Nova Alocação
                    </h6>
                    <form id="formTurno">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Motorista</label>
                            <select class="form-select form-select-sm" required>
                                <option value="">Selecione...</option>
                                <option>Carlos Alberto</option>
                                <option>Maria Oliveira</option>
                                <option>João Silva</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Veículo / Prefixo</label>
                            <select class="form-select form-select-sm" required>
                                <option value="">Selecione...</option>
                                <option>#2035 - Placa ABC-1234</option>
                                <option>#1012 - Placa XYZ-9876</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm w-100 fw-bold shadow-sm" onclick="alert('Turno iniciado com sucesso!')">
                            INICIAR TURNO
                        </button>
                    </form>
                </div>

                <div class="flex-grow-1 overflow-auto">
                    <h6 class="fw-bold mb-3 d-flex justify-content-between">
                        <span>Veículos em Rota</span>
                        <span class="badge bg-primary rounded-pill">2</span>
                    </h6>
                    <div class="list-group list-group-flush border rounded overflow-hidden">
                        <div class="list-group-item p-3 border-0 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold small">Veículo #2035</span>
                                <span class="badge bg-success-subtle text-success small border border-success-subtle">Ativo</span>
                            </div>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                <i class="bi bi-person me-1"></i> Carlos Alberto <br>
                                <i class="bi bi-clock me-1"></i> Início: 08:30h
                            </div>
                        </div>
                        <div class="list-group-item p-3 border-0">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold small">Veículo #1012</span>
                                <span class="badge bg-warning-subtle text-warning small border border-warning-subtle">Parado</span>
                            </div>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                <i class="bi bi-person me-1"></i> Maria Oliveira <br>
                                <i class="bi bi-clock me-1"></i> Início: 09:15h
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <div class="alert alert-info py-2 mb-0" style="font-size: 0.7rem;">
                        <i class="bi bi-info-circle-fill me-1"></i> Dados atualizados a cada 30 segundos.
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <!-- Scripts do Mapa -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializa o mapa focado em uma região central (Ex: Itapetininga)
            const map = L.map('map-canvas', {
                zoomControl: false,
                attributionControl: false
            }).setView([-23.5900, -48.0500], 14);

            L.control.zoom({ position: 'bottomright' }).addTo(map);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            const busIcon = L.divIcon({
                className: 'bus-marker-container',
                html: '<div class="bus-marker" style="width:30px;height:30px;"><i class="bi bi-bus-front"></i></div>',
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });

            // Marcadores de exemplo
            L.marker([-23.5880, -48.0450], {icon: busIcon}).addTo(map)
                .bindPopup("<b>Veículo #2035</b><br>Status: Em Movimento<br>Velocidade: 35 km/h");

            L.marker([-23.5950, -48.0600], {icon: busIcon}).addTo(map)
                .bindPopup("<b>Veículo #1012</b><br>Status: Ponto de Parada<br>Tempo: 2 min");
        });
    </script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>