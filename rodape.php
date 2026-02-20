<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>rodapé - público</title>

    <!-- CSS específico -->
    <link rel="stylesheet" href="./css/meu_estilo.css">

    <!-- Fonte local -->
    <link rel="stylesheet" href="./css/fonts.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="./css/bootstrap.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="./css/bootstrap-icons.css">
</head>
<body>

    <footer class="bg-dark text-light pt-5 pb-4 py-4">
        <div class="container">
            <div class="row">

                <div class="col-md-3 mb-4">
                    <h5 class="fw-bold"><strong class="bi bi-bus-front"></strong>&nbsp;&nbsp;Transporte</h5>
                    <p class="small text-secondary">
                        Dados de ônibus em tempo real: horários, rotas e chegadas para facilitar sua rotina.
                    </p>
                </div>

                <div class="col-md-2 mb-4">
                    <h5 class="fw-bold text-uppercase small">Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="./index.php" class="text-secondary text-decoration-none small">Início</a></li>
                        <li><a href="./admin/login.php" class="text-secondary text-decoration-none small">Administrativo</a></li>
                        <li><a href="./sobre.php" class="text-secondary text-decoration-none small">Sobre nós</a></li>
                    </ul>
                </div>

                <div class="col-md-3 mb-4">
                    <h5 class="fw-bold text-uppercase small">Siga-nos</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light fs-5"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-light fs-5"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-light fs-5"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="text-light fs-5"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold text-uppercase small mb-3">Fale Conosco</h5>
                    <form
                        action="rodape_contato_envia.php"
                        name="form_contato"
                        id="form_contato"
                        method="post"
                    >
                        <div class="mb-2 input-group">
                            <span class="input-group-addon" id="basic-addon1"></span>
                            <input 
                                type="text" 
                                class="form-control form-control-sm bg-secondary text-white border-0 shadow-none"
                                id="nome_contato"
                                name="nome_contato"
                                placeholder="Digite seu nome."
                                aria-describedby="basic-addon1"
                                required
                            >
                        </div>
                        <div class="mb-2">
                            <span class="input-group-addon" id="basic-addon2"></span>
                            <input 
                                type="email"
                                name="email_contato"
                                id="email_contato"
                                class="form-control form-control-sm bg-secondary text-white border-0 shadow-none" 
                                placeholder="Digite seu e-mail."
                                aria-describedby="basic-addon2" 
                                required
                            >
                        </div>
                        <div class="mb-2">
                            <span class="input-group-addon" id="basic-addon3"></span>
                            <textarea 
                                class="form-control form-control-sm bg-secondary text-white border-0 shadow-none" 
                                name="comentarios_contato"
                                id="comentarios_contato"
                                cols="30"
                                rows="5" 
                                placeholder="Comentários, dúvidas e/ou sugestões."
                                aria-describedby="basic-addon3" 
                                required
                            ></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold" arial-label="Enviar">ENVIAR</button>
                    </form>
                </div>

            </div> <hr class="border-secondary opacity-25">

            <div class="text-center small text-secondary">
                © 2026 Transporte Público. Todos os direitos reservados.
            </div>
        </div>
    </footer>

<!-- Bootstrap JS -->
<script src="./js/bootstrap.bundle.min.js"></script>
</body>
</html>