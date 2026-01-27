<?php
// OBRIGATÓRIO: Proteção de Login e início da sessão
include 'verificar_login.php'; 

// Incluir a conexão - Caminho padrão do seu projeto
include("../connections/db_connect.php");

// Variável para o nome do seu banco de dados
$database_conn = "TransportePublico_ti19";

// Selecionar o banco de dados
mysqli_select_db($conn, $database_conn);

// 1. Consulta para motoristas (para a gestão de turnos)
$sql_motoristas = "SELECT id_motorista, nome FROM tbmotoristas ORDER BY nome ASC";
$res_motoristas = $conn->query($sql_motoristas);

// 2. Consulta de veículos ativos
$sql_veiculos = "SELECT v.id_veiculo, v.prefixo, v.placa, l.nome as linha_nome 
                 FROM tbveiculos v 
                 LEFT JOIN tblinhas l ON v.id_linha = l.id_linha";
$res_veiculos = $conn->query($sql_veiculos);

$total_veiculos = ($res_veiculos) ? $res_veiculos->num_rows : 0;

$titulo_pagina = "Painel de Monitoramento";

// Inclui o header padrão do admin (Navbar e Bootstrap)
include 'header.php'; 
?>

<!-- Importação do Leaflet CSS para o Mapa -->
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
    .custom-bus i {
        font-size: 1.2rem;
    }
</style>

<div class="container-fluid mt-4 px-4">
    <!-- Cabeçalho de Monitorização -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary fw-bold mb-0">
                <i class="bi bi-broadcast"></i> Centro de Monitoramento
            </h2>
            <p class="text-muted">Localização em Tempo Real e Gestão de Escalas</p>
        </div>
        <div class="text-end">
            <span class="badge bg-light text-dark border p-2">
                <span class="pulse-online"></span> Ligação Ativa
            </span>
            <div class="small text-muted mt-1" id="clock">--:--:--</div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Mapa de Monitorização -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-map"></i> Mapa da Frota</h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="window.location.reload()">
                        <i class="bi bi-arrow-clockwise"></i> Atualizar GPS
                    </button>
                </div>
                <div class="card-body p-0">
                    <div id="mapa-monitoramento"></div>
                </div>
            </div>
        </div>

        <!-- Lateral: Controles Operacionais -->
        <div class="col-lg-4">
            <!-- Estatística Rápida -->
            <div class="card shadow-sm border-0 mb-4 card-stats">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small fw-bold">Veículos no Sistema</h6>
                    <h3 class="fw-bold"><?php echo $total_veiculos; ?></h3>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                    </div>
                </div>
            </div>

            <!-- Formulário de Atribuição de Turno -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-person-plus"></i> Abrir Turno Operacional</h5>
                </div>
                <div class="card-body">
                    <form id="formTurno" action="#" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">MOTORISTA</label>
                            <select name="id_motorista" class="form-select border-primary-subtle" required>
                                <option value="">Selecione um motorista...</option>
                                <?php if($res_motoristas): while($m = $res_motoristas->fetch_assoc()): ?>
                                    <option value="<?php echo $m['id_motorista']; ?>"><?php echo htmlspecialchars($m['nome']); ?></option>
                                <?php endwhile; endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">VEÍCULO / PREFÍXO</label>
                            <select name="id_veiculo" class="form-select border-primary-subtle" required>
                                <option value="">Selecione a viatura...</option>
                                <?php 
                                if($res_veiculos):
                                    $res_veiculos->data_seek(0); // Reiniciar o ponteiro
                                    while($v = $res_veiculos->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $v['id_veiculo']; ?>"><?php echo htmlspecialchars($v['prefixo']); ?> - <?php echo htmlspecialchars($v['placa']); ?></option>
                                <?php endwhile; endif; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">HORA INÍCIO</label>
                                <input type="time" name="hora_inicio" class="form-control" value="<?php echo date('H:i'); ?>" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">DURAÇÃO (H)</label>
                                <input type="number" name="duracao" class="form-control" value="8" min="1" max="12">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                            <i class="bi bi-check-circle"></i> CONFIRMAR ESCALA
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabela de Turnos Ativos -->
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Turnos em Operação</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Motorista</th>
                                <th>Veículo</th>
                                <th>Linha Atual</th>
                                <th>Início</th>
                                <th>Saída Prevista</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Exemplo Estático -->
                            <tr>
                                <td class="fw-bold">Ricardo Santos</td>
                                <td><span class="badge bg-dark">#B-102</span></td>
                                <td>Linha Norte-Sul</td>
                                <td>08:00</td>
                                <td>16:00</td>
                                <td><span class="badge bg-success">Em Rota</span></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-danger" title="Fechar Turno">
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

<!-- Scripts de Mapa e Funções -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Relógio do Sistema
        setInterval(() => {
            const clock = document.getElementById('clock');
            if(clock) clock.innerText = new Date().toLocaleTimeString('pt-PT');
        }, 1000);

        // Inicializar Mapa
        const map = L.map('mapa-monitoramento').setView([38.7223, -9.1393], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Estilo do Ícone do Ônibus
        const busIcon = L.divIcon({
            className: 'custom-bus',
            html: '<div style="background:#0d6efd; color:white; width:34px; height:34px; line-height:34px; border-radius:50%; text-align:center; border:3px solid white; box-shadow:0 0 10px rgba(0,0,0,0.3)"><i class="bi bi-bus-front"></i></div>',
            iconSize: [34, 34]
        });

        // Marcadores de Exemplo
        L.marker([38.7250, -9.1400], {icon: busIcon}).addTo(map).bindPopup('<b>Veículo #B-102</b><br>Motorista: Ricardo Santos<br><span class="text-success">Status: OK</span>');
        L.marker([38.7320, -9.1350], {icon: busIcon}).addTo(map).bindPopup('<b>Veículo #A-405</b><br>Estado: Parado em Ponto');

        // Tratamento do Formulário (Exemplo Simulado)
        document.getElementById('formTurno').onsubmit = function(e) {
            e.preventDefault();
            alert('Sucesso: Turno atribuído e motorista alocado no mapa.');
        };
    });
</script>

<?php 
// Liberação de memória e encerramento
if ($res_motoristas) $res_motoristas->free();
if ($res_veiculos) $res_veiculos->free();
$conn->close();

include 'footer.php'; 
?>