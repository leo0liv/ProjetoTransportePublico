<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linhas De Onibus</title>
   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <style>
        body { background-color: #f8f9fa; }
        .header-simple { padding: 40px 0; text-align: center; color: #333; }
        .bus-card { transition: transform 0.2s; cursor: pointer; overflow: hidden; }
        .bus-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
        .bus-img { width: 100%; height: 160px; object-fit: cover; }
        .badge-line { font-size: 1.2rem; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
    </style>
</head> 
<body>s
    <?php 
   
        include("../src/menu.php"); 
    ?>

    <header class="header-simple">
        <div class="container">
            <h1 class="display-5 fw-bold">Linhas de Ônibus</h1>
            <p class="lead">Consulte abaixo as linhas disponíveis</p>
        </div>
    </header>

    <div class="container mb-5">
        <div class="row g-4" id="busContainer">
            
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const linhasDeOnibus = [
            { id: "101", nome: "Centro / Bairro Real", cor: "bg-primary", status: "Operando", img: "https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&w=400&q=80" },
            { id: "205", nome: "Terminal Norte / Shopping", cor: "bg-success", status: "Operando", img: "https://images.unsplash.com/photo-1570125909232-eb263c188f7e?auto=format&fit=crop&w=400&q=80" },
            { id: "312", nome: "Universidade / Estação", cor: "bg-danger", status: "Atrasado", img: "https://images.unsplash.com/photo-1632276514732-c3a506649f8a?auto=format&fit=crop&w=400&q=80" },
            { id: "404", nome: "Circular Aeroporto", cor: "bg-warning text-dark", status: "Operando", img: "https://images.unsplash.com/photo-1590610931215-062e7428801d?auto=format&fit=crop&w=400&q=80" },
            { id: "550", nome: "Vila Nova / Industrial", cor: "bg-info text-dark", status: "Manutenção", img: "https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&w=400&q=80" },
            { id: "010", nome: "Expresso Praiano", cor: "bg-dark", status: "Operando", img: "https://images.unsplash.com/photo-1570125909232-eb263c188f7e?auto=format&fit=crop&w=400&q=80" }
        ];

        const container = document.getElementById('busContainer');

        function displayLines(linhas) {
            container.innerHTML = "";
            
            linhas.forEach(linha => {
                const card = `
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 bus-card border-0 shadow-sm">
                            <img src="${linha.img}" alt="${linha.nome}" class="bus-img">
                            <div class="card-body d-flex align-items-center">
                                <div class="${linha.cor} text-white fw-bold badge-line me-3 shadow-sm">
                                    ${linha.id}
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1" style="font-size: 1.1rem;">${linha.nome}</h5>
                                    <p class="card-text mb-0">
                                        <span class="badge ${linha.status === 'Atrasado' ? 'bg-warning' : 'bg-light text-dark'} border">
                                            ${linha.status}
                                        </span>
                                    </p>
                                </div>
                                <span class="material-icons text-secondary">chevron_right</span>
                            </div>
                        </div>
                    </div>
                `;
                container.innerHTML += card;
            });
        }

        displayLines(linhasDeOnibus);
    </script>
</body>
</html>