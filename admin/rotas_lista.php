<?php
// 1. Configurações Iniciais
$titulo_pagina = "Gerenciamento de Rotas";
include 'verificar_login.php'; 
include("../connections/db_connect.php");

// 2. Consulta ao Banco de Dados
// CORREÇÃO: Agora contamos quantos HORÁRIOS (viagens) a linha tem,
// pois os pontos estão ligados aos horários, e não mais diretamente à linha.
$sql = "
    SELECT 
        L.id_linha, 
        L.codigo, 
        L.nome, 
        (SELECT COUNT(*) FROM tbhorario_programados H WHERE H.id_linha = L.id_linha) as qtd_viagens
    FROM tblinhas L
    ORDER BY L.codigo ASC
";

$resultado = $conn->query($sql);

// Inclui o menu/topo
include '../admin/header.php'; 
?>
<style>
    body { 
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', sans-serif; 
        }
    .hover-card { transition: transform 0.2s; }
    .hover-card:hover { transform: translateY(-5px); }
    
</style>
<body>
    <div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary mb-4">
            <i class="bi bi-diagram-3-fill"></i> Gerenciamento de Rotas
        </h2>
    </div>

    <div class="alert alert-info shadow-sm d-flex align-items-center" role="alert">
        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
        <div>
            Selecione uma linha abaixo para configurar os <strong>Horários de Saída</strong> e seus respectivos itinerários.
        </div>
    </div>

    <div class="row g-4">
        
        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <?php while($linha = $resultado->fetch_assoc()): ?>
                
                <?php 
                    // Se tiver viagens cadastradas, fica verde. Se não, amarelo.
                    $badgeClass = ($linha['qtd_viagens'] > 0) ? 'bg-success' : 'bg-warning text-dark'; 
                ?>

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 hover-card">
                        <div class="card-body">
                            
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-secondary fs-6"><?php echo $linha['codigo']; ?></span>
                                <span class="badge <?php echo $badgeClass; ?>">
                                    <?php echo $linha['qtd_viagens']; ?> Horários
                                </span>
                            </div>

                            <h5 class="card-title fw-bold text-dark"><?php echo $linha['nome']; ?></h5>
                            <p class="card-text text-muted small">
                                Gerencie os horários de partida e os pontos de parada desta linha.
                            </p>
                            
                            <div class="d-grid mt-4">
                                <a href="rotas_gerenciar.php?id_linha=<?php echo $linha['id_linha']; ?>" class="btn btn-primary">
                                    <i class="bi bi-clock-history"></i> Ver Horários & Pontos
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle"></i> Nenhuma linha encontrada ou erro na consulta. 
                    <a href="linhas.php" class="alert-link">Cadastre uma linha primeiro</a>.
                </div>
            </div>

        <?php endif; ?>

    </div>
</div>



<?php 
$conn->close();
include '../admin/footer.php'; 
?>
</body>