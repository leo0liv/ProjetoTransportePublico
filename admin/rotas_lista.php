<?php
// 1. Configurações Iniciais
$titulo_pagina = "Gerenciamento de Rotas";
include 'verificar_login.php'; 
include("../connections/db_connect.php");

// 2. Consulta ao Banco de Dados
// CORREÇÃO: Removida a coluna 'descricao' que não existe no seu banco
$sql = "
    SELECT 
        L.id_linha, 
        L.codigo, 
        L.nome, 
        (SELECT COUNT(*) FROM tbrotas R WHERE R.id_linha = L.id_linha) as qtd_pontos
    FROM tblinhas L
    ORDER BY L.codigo ASC
";

$resultado = $conn->query($sql);

// Inclui o menu/topo
include '../admin/header.php'; 
?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary fw-bold">
            <i class="bi bi-diagram-3-fill"></i> Gerenciamento de Rotas
        </h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Rotas</li>
            </ol>
        </nav>
    </div>

    <div class="alert alert-info shadow-sm d-flex align-items-center" role="alert">
        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
        <div>
            Selecione uma linha abaixo para configurar os pontos de parada e a ordem do itinerário.
        </div>
    </div>

    <div class="row g-4">
        
        <?php if ($resultado->num_rows > 0): ?>
            <?php while($linha = $resultado->fetch_assoc()): ?>
                
                <?php 
                    $badgeClass = ($linha['qtd_pontos'] > 0) ? 'bg-success' : 'bg-warning text-dark'; 
                ?>

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 hover-card">
                        <div class="card-body">
                            
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-secondary fs-6"><?php echo $linha['codigo']; ?></span>
                                <span class="badge <?php echo $badgeClass; ?>">
                                    <?php echo $linha['qtd_pontos']; ?> Pontos
                                </span>
                            </div>

                            <h5 class="card-title fw-bold text-dark"><?php echo $linha['nome']; ?></h5>
                            <p class="card-text text-muted small">
                                Configure o trajeto desta linha adicionando paradas cadastradas.
                            </p>
                            
                            <div class="d-grid mt-4">
                                <a href="rotas_gerenciar.php?id_linha=<?php echo $linha['id_linha']; ?>" class="btn btn-primary">
                                    <i class="bi bi-geo-alt-fill"></i> Gerenciar Itinerário
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle"></i> Nenhuma linha encontrada. 
                    <a href="linhas.php" class="alert-link">Cadastre uma linha primeiro</a>.
                </div>
            </div>

        <?php endif; ?>

    </div>
</div>

<style>
    .hover-card { transition: transform 0.2s; }
    .hover-card:hover { transform: translateY(-5px); }
</style>

<?php 
$conn->close();
include '../admin/footer.php'; 
?>