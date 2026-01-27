<?php
// OBRIGATÓRIO: Proteção de Login e início da sessão
include 'verificar_login.php'; 

// Incluir a conexão
include("../connections/db_connect.php");

// Variável para o nome do seu banco de dados
$database_conn = "TransportePublico";
// Selecionar o banco de dados
mysqli_select_db($conn, $database_conn);

// 1. Consulta para motoristas (para a gestão de turnos)
$sql_motoristas = "SELECT id_motorista, nome FROM tbmotoristas ORDER BY nome ASC";
$res_motoristas = $conn->query($sql_motoristas);

// 2. Consulta de veículos ativos (simulando monitoramento)
$sql_veiculos = "SELECT v.id_veiculo, v.prefixo, v.placa, l.nome as linha_nome 
                 FROM tbveiculos v 
                 LEFT JOIN tblinhas l ON v.id_linha = l.id_linha";
$res_veiculos = $conn->query($sql_veiculos);

$titulo_pagina = "Painel de Monitoramento";
// Ajuste do caminho do header conforme os seus arquivos enviados
include 'header.php'; 
?>

<!-- Importação do Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    #mapa-monitoramento {
        height: 500px;
        width: 100%;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        z-index: 1;
    }
    .card-stats {
        border-left: 4px solid #0d6efd;
    }
    .pulse-online {
        display: inline-block;
        width: 10px;
        height: 10px;
        background-color: #198754;
        border-radius: 50%;
        margin-right: 5px;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
    }
    .custom-bus-icon i {
        font-size: 18px;
    }
</style>

<div class="container-fluid mt-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary fw-bold mb-0">
                <i class="bi bi-broadcast"></i> Centro de Monitoramento
            </h2>
            <p class="text-muted">Gestão operacional e localização em tempo real</p>
        </div>
        <div class="text-end">
            <span class="badge bg-light text-dark border p-2">
                <span class="pulse-online"></span> Sistema Ativo
            </span>
            <div class="small text-muted mt-1" id="clock">--:--:--</div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Mapa de Localização -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Mapa da Frota</h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="window.location.reload()">
                        <i class="bi bi-arrow-clockwise"></i> Atualizar
                    </button>
                </div>
                <div class="card-body p-0">
                    <div id="mapa-monitoramento"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar Operacional -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4 card-stats">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small fw-bold">Veículos Registados</h6>
                    <h3 class="fw-bold"><?php echo ($res_veiculos) ? $res_veiculos->num_rows : 0; ?></h3>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                </div>
            </div>

            <!-- Formulário de Turno -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history"></i> Iniciar Novo Turno</h5>
                </div>
                <div class="card-body">
                    <form id="formTurno">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Motorista</label>
                            <select class="form-select" required>
                                <option value="">Selecione o motorista...</option>
                                <?php if($res_motoristas): while($m = $res_motoristas->fetch_assoc()): ?>
                                    <option value="<?php echo $m['id_motorista']; ?>"><?php echo htmlspecialchars($m['nome']); ?></option>
                                <?php endwhile; endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Veículo / Prefixo</label>
                            <select class="form-select" required>
                                <option value="">Selecione o veículo...</option>
                                <?php 
                                if($res_veiculos):
                                    $res_veiculos->data_seek(0);
                                    while($v = $res_veiculos->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $v['id_veiculo']; ?>"><?php echo htmlspecialchars($v['prefixo']); ?> (<?php echo htmlspecialchars($v['placa']); ?>)</option>
                                <?php endwhile; endif; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold">Hora Entrada</label>
                                <input type="time" class="form-control" value="<?php echo date('H:i'); ?>">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold">Duração (h)</label>
                                <input type="number" class="form-control" value="8" min="1" max="12">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">
                            <i class="bi bi-check2-circle"></i> Confirmar Início
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabela de Turnos -->
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Turnos em Andamento (Simulação)</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Motorista</th>
                                <th>Veículo</th>
                                <th>Linha</th>
                                <th>Entrada</th>
                                <th>Previsão Saída</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><i class="bi bi-person-circle me-2"></i> Operador Exemplo</td>
                                <td><span class="badge bg-secondary">B-102</span></td>
                                <td>Rota Principal</td>
                                <td><?php echo date('H:i', strtotime('-2 hours')); ?></td>
                                <td><?php echo date('H:i', strtotime('+6 hours')); ?></td>
                                <td><span class="badge bg-success">Ativo</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger" title="Encerrar Turno">
                                        <i class="bi bi-stop-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Relógio em tempo real
        setInterval(() => {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString('pt-PT');
        }, 1000);

        // Inicialização do Mapa (Focado em Portugal por padrão)
        const map = L.map('mapa-monitoramento').setView([38.7223, -9.1393], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Ícone customizado para os veículos
        const busIcon = L.divIcon({
            className: 'custom-bus-icon',
            html: '<div style="background:#0d6efd; color:white; padding:5px; border-radius:50%; border:2px solid white; box-shadow:0 0 10px rgba(0,0,0,0.3); text-align:center;"><i class="bi bi-bus-front"></i></div>',
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });

        // Marcadores de exemplo (Simulando coordenadas)
        const pontos = [
            { lat: 38.7250, lng: -9.1400, info: "Veículo B-102" },
            { lat: 38.7300, lng: -9.1350, info: "Veículo A-405" }
        ];

        pontos.forEach(p => {
            L.marker([p.lat, p.lng], { icon: busIcon })
                .addTo(map)
                .bindPopup(`<b>${p.info}</b><br>Estado: Em Movimento<br><span class="text-success">● GPS Ativo</span>`);
        });

        // Evento do formulário
        document.getElementById('formTurno').onsubmit = function(e) {
            e.preventDefault();
            alert('Operação simulada: Turno registado com sucesso!');
        };
    });
</script>

<?php 
// Liberação de memória e fecho da ligação
if ($res_motoristas) mysqli_free_result($res_motoristas);
if ($res_veiculos) mysqli_free_result($res_veiculos);
mysqli_close($conn);

include 'footer.php'; 
?>