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
    body { 
        /* Substitua o link abaixo pelo link da sua imagem ou caminho local */
        background-image: url('https://i.pinimg.com/736x/d3/8f/a9/d38fa9bdada897b12a59afd0ee968b4b.jpg'); 
        
        /* Configurações para a imagem NÃO esticar */
        background-size: cover;           /* Cobre a tela mantendo a proporção */
        background-position: center;      /* Centraliza a imagem */
        background-repeat: no-repeat;     /* Não deixa a imagem repetir */
        background-attachment: fixed;     /* A imagem fica parada enquanto o código rola */
        
        background-color: #f8f9fa;        /* Cor reserva caso a imagem falhe */
    }

    .header-simple { 
        padding: 40px 0; 
        text-align: center; 
        color: #fff;                      /* Texto branco para aparecer sobre a foto */
        background-color: rgba(0, 0, 0, 0.5); /* Fundo escuro transparente para o título */
        margin-bottom: 30px;
    }

    .bus-card { transition: transform 0.2s; cursor: pointer; overflow: hidden; background-color: #fff; }
    .bus-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.3); }
    .bus-img { width: 100%; height: 160px; object-fit: cover; }
    .badge-line { font-size: 1.2rem; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
</style>
</head> 
<body>

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
            { id: "101", nome: "Centro / Bairro Real", cor: "bg-primary", status: "Operando", img: "https://i.pinimg.com/1200x/1c/ec/9e/1cec9e718927991e0b869ef554d9d417.jpg" },
            { id: "205", nome: "Terminal Norte / Shopping", cor: "bg-success", status: "Operando", img: "https://i.pinimg.com/736x/7b/16/37/7b1637552bd622c86351b12e8fbb53bf.jpg" },
            { id: "312", nome: "Universidade / Estação", cor: "bg-danger", status: "Atrasado", img: "https://i.pinimg.com/736x/50/dd/80/50dd8042d89cc9deb6e0c9f4f5566fcc.jpg" },
            { id: "404", nome: "Circular Aeroporto", cor: "bg-warning text-dark", status: "Operando", img: "https://i.pinimg.com/1200x/df/ea/9c/dfea9c91925044600dc8ce5b6b9a5c2e.jpg" },
            { id: "550", nome: "Vila Nova / Industrial", cor: "bg-info text-dark", status: "Manutenção", img: "https://i.pinimg.com/736x/52/fd/2e/52fd2e0e5a3fda603435e7e1f027413c.jpg" },
            { id: "010", nome: "Expresso Praiano", cor: "bg-dark", status: "Operando", img: "https://i.pinimg.com/736x/fb/c6/22/fbc6227bd878e8e48d145757d0c9f1e8.jpg" }
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