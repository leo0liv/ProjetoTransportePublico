<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área Administrativa/Operacional</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Leaflet CSS (Mapa OpenSource) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        :root {
            --cor-primaria: #0d6efd;
            --cor-fundo: #f3f4f6;
            --sidebar-width: 280px;
        }

        body {
            background-color: var(--cor-fundo);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
        }

        /* Utilitário para ocultar/mostrar secções */
        .d-none-custom {
            display: none !important;
        }

        /* Estilos do Login */
        .login-wrapper {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }
        .card-login {
            width: 100%;
            max-width: 400px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        /* Estilos do Dashboard */
        .sidebar {
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            background-color: #212529;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            transition: all 0.3s;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            transition: all 0.3s;
        }

        .nav-link {
            color: rgba(255, 255, 255, .75);
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-link:hover, .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,0.1);
        }

        /* Mapa */
        #mapa-monitoramento {
            height: 450px;
            width: 100%;
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            z-index: 1;
        }

        /* Animação do Badge */
        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); }
            100% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
        }
        .pulse-animation {
            animation: pulse-green 2s infinite;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar.show {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>

    <!-- ================= ECRÃ DE LOGIN ================= -->
    <div id="ecra-login" class="login-wrapper">
        <div class="card card-login bg-white p-4">
            <div class="text-center mb-4">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-shield-lock-fill fs-3"></i>
                </div>
                <h4 class="fw-bold text-dark">Área Operacional</h4>
                <p class="text-muted small">Faça login para gerir turnos e monitorizar</p>
            </div>

            <div id="msg-erro" class="alert alert-danger py-2 d-none align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div id="texto-erro">Credenciais inválidas.</div>
            </div>

            <form id="formLogin">
                <div class="mb-3">
                    <label for="usuario" class="form-label fw-semibold">Utilizador</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="usuario" value="admin" placeholder="ex: admin" required autofocus>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="senha" class="form-label fw-semibold">Palavra-passe</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                        <input type="password" class="form-control" id="senha" value="1234" placeholder="ex: 1234" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                    Entrar no Sistema <i class="bi bi-arrow-right-short"></i>
                </button>
            </form>
            <div class="text-center mt-3">
                <small class="text-muted">Acesso restrito a supervisores.</small>
            </div>
        </div>
    </div>

    <!-- ================= DASHBOARD (Inicialmente Oculto) ================= -->
    <div id="dashboard-admin" class="d-none-custom">
        
        <!-- Navbar Mobile -->
        <nav class="navbar navbar-dark bg-dark d-md-none p-3 mb-3">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <span class="navbar-brand m-0 h1">Sistema OPS</span>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                    <div class="position-sticky pt-3">
                        <div class="text-center mb-4 text-white">
                            <i class="bi bi-buildings fs-1"></i>
                            <h5 class="mt-2">Centro de Controlo</h5>
                            <small class="text-white-50">v1.0.2</small>
                        </div>
                        
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link active" href="#" onclick="alert('Funcionalidade de Dashboard Geral')">
                                    <i class="bi bi-speedometer2"></i>
                                    Painel Principal
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#monitoramento">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    Monitorização GPS
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#turnos">
                                    <i class="bi bi-calendar-check"></i>
                                    Gestão de Turnos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="bi bi-people"></i>
                                    Operadores
                                </a>
                            </li>
                        </ul>

                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                            <span>Sistema</span>
                        </h6>
                        <ul class="nav flex-column mb-2">
                            <li class="nav-item">
                                <a class="nav-link text-danger" href="#" onclick="logoutSistema()">
                                    <i class="bi bi-box-arrow-left"></i>
                                    Terminar Sessão
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>

                <!-- Conteúdo Principal -->
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Área Administrativa / Operacional</h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <div class="btn-group me-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary">Partilhar</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary">Exportar</button>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle">
                                <i class="bi bi-calendar"></i> Esta semana
                            </button>
                        </div>
                    </div>

                    <!-- Secção de Monitorização -->
                    <div class="row mb-4" id="monitoramento">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                                    <h5 class="mb-0 text-primary"><i class="bi bi-broadcast me-2"></i>Localização em Tempo Real</h5>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success pulse-animation">
                                        ● Sistema Online
                                    </span>
                                </div>
                                <div class="card-body p-0 position-relative">
                                    <!-- Mapa Leaflet -->
                                    <div id="mapa-monitoramento"></div>
                                    
                                    <!-- Overlay de Informação -->
                                    <div class="position-absolute bottom-0 start-0 m-3 p-3 bg-white rounded shadow" style="z-index: 1000; min-width: 250px; border-left: 4px solid #0d6efd;">
                                        <h6 class="fw-bold mb-2">Dados da Unidade #01</h6>
                                        <div class="small text-muted mb-1">Status: <span class="text-success fw-bold">Em Movimento</span></div>
                                        <div class="small text-muted mb-1">Velocidade: <strong>45 km/h</strong></div>
                                        <div class="small text-muted">Última atualização: <span id="last-update">--:--:--</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Secção de Gestão de Turnos -->
                    <div class="row" id="turnos">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Escala de Turnos</h5>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNovoTurno">
                                        <i class="bi bi-plus-lg"></i> Novo Turno
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Operador</th>
                                                <th>Local / Posto</th>
                                                <th>Entrada</th>
                                                <th>Saída (Prevista)</th>
                                                <th>Status</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabela-turnos">
                                            <!-- Dados Estáticos -->
                                            <tr>
                                                <td>#TRN-104</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-secondary text-white rounded-circle d-flex justify-content-center align-items-center me-2" style="width:30px; height:30px;">JP</div>
                                                        <div>João Pereira</div>
                                                    </div>
                                                </td>
                                                <td>Zona Norte - Posto 4</td>
                                                <td>08:00</td>
                                                <td>16:00</td>
                                                <td><span class="badge bg-success">Em Curso</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-light text-primary"><i class="bi bi-pencil"></i></button>
                                                    <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>#TRN-105</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-secondary text-white rounded-circle d-flex justify-content-center align-items-center me-2" style="width:30px; height:30px;">AS</div>
                                                        <td>Ana Silva</td>
                                                    </div>
                                                </td>
                                                <td>Centro - Sede</td>
                                                <td>14:00</td>
                                                <td>22:00</td>
                                                <td><span class="badge bg-warning text-dark">Agendado</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-light text-primary"><i class="bi bi-pencil"></i></button>
                                                    <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <footer class="mt-5 text-muted text-center small">
                        &copy; 2024 Sistema de Gestão Operacional. Todos os direitos reservados.
                    </footer>
                </main>
            </div>
        </div>
    </div>

    <!-- Modal Novo Turno -->
    <div class="modal fade" id="modalNovoTurno" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registar Novo Turno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formTurno">
                        <div class="mb-3">
                            <label class="form-label">Nome do Operador</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Hora Entrada</label>
                                <input type="time" class="form-control" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Hora Saída</label>
                                <input type="time" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Posto de Trabalho</label>
                            <select class="form-select">
                                <option>Zona Norte</option>
                                <option>Zona Sul</option>
                                <option>Centro Operacional</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="adicionarTurnoSimulado()">Salvar Turno</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // --- VARIÁVEIS GLOBAIS ---
        let map = null;
        let marker = null;
        
        // --- 1. Lógica de Login (Simulação) ---
        document.getElementById('formLogin').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const user = document.getElementById('usuario').value;
            const pass = document.getElementById('senha').value;
            const msgErro = document.getElementById('msg-erro');
            
            // Simulação de Credenciais
            if(user === 'admin' && pass === '1234') {
                // Login Sucesso
                msgErro.classList.add('d-none');
                
                // Transição de Ecrã
                document.getElementById('ecra-login').classList.add('d-none-custom');
                document.getElementById('dashboard-admin').classList.remove('d-none-custom');
                
                // Inicializar Mapa após mostrar a div (importante para o Leaflet calcular o tamanho correto)
                setTimeout(iniciarMapa, 200);
            } else {
                // Login Erro
                msgErro.classList.remove('d-none');
                document.getElementById('texto-erro').innerText = "Credenciais inválidas. Tente admin / 1234";
            }
        });

        function logoutSistema() {
            document.getElementById('dashboard-admin').classList.add('d-none-custom');
            document.getElementById('ecra-login').classList.remove('d-none-custom');
            document.getElementById('senha').value = '';
        }

        // --- 2. Inicialização do Mapa (Leaflet) ---
        function iniciarMapa() {
            if (map !== null) return; // Evita reinicializar

            var startLat = 38.7223; // Lisboa Exemplo
            var startLng = -9.1393;

            map = L.map('mapa-monitoramento').setView([startLat, startLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            var iconOperacional = L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color:#0d6efd; width:15px; height:15px; border-radius:50%; border:2px solid white; box-shadow: 0 0 10px rgba(0,0,0,0.5);'></div>",
                iconSize: [15, 15],
                iconAnchor: [7, 7]
            });

            marker = L.marker([startLat, startLng], {icon: iconOperacional}).addTo(map)
                .bindPopup("<b>Central Operacional</b><br>Monitorização Ativa").openPopup();

            // Iniciar Simulação de GPS
            monitorarGPS();
        }

        // --- 3. Simulação de Localização em Tempo Real ---
        function monitorarGPS() {
            // Tenta GPS real, fallback para simulação
            if (navigator.geolocation) {
                navigator.geolocation.watchPosition(function(position) {
                    atualizarMarcador(position.coords.latitude, position.coords.longitude);
                }, function(error) {
                    console.log("GPS bloqueado. Usando simulação.");
                    simularMovimento();
                });
            } else {
                simularMovimento();
            }
        }

        function simularMovimento() {
            let lat = 38.7223;
            let lng = -9.1393;
            
            setInterval(() => {
                // Move aleatoriamente
                lat += (Math.random() - 0.5) * 0.001;
                lng += (Math.random() - 0.5) * 0.001;
                atualizarMarcador(lat, lng);
            }, 3000);
        }

        function atualizarMarcador(lat, lng) {
            if(!marker) return;
            marker.setLatLng([lat, lng]);
            map.panTo([lat, lng]);
            
            const agora = new Date();
            document.getElementById('last-update').innerText = agora.toLocaleTimeString();
        }

        // --- 4. Adicionar Turno (Frontend) ---
        function adicionarTurnoSimulado() {
            const tbody = document.getElementById('tabela-turnos');
            const novoRow = `
                <tr class="table-info">
                    <td>#TRN-NEW</td>
                    <td><div class="d-flex align-items-center"><div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-2" style="width:30px; height:30px;">NV</div>Novo Operador</div></td>
                    <td>Posto Remoto</td>
                    <td>09:00</td>
                    <td>17:00</td>
                    <td><span class="badge bg-primary">Iniciado</span></td>
                    <td>
                        <button class="btn btn-sm btn-light text-primary"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('afterbegin', novoRow);
            
            // Fechar modal
            const modalEl = document.getElementById('modalNovoTurno');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
        }
    </script>
</body>
</html>