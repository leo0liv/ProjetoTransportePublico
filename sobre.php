<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nós - Nossa Equipe</title>
    
    <!-- CSS específico -->
    <link rel="stylesheet" href="./css/meu_estilo.css">

    <!-- Fonte local -->
    <link rel="stylesheet" href="./css/fonts.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="./css/bootstrap.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="./css/bootstrap-icons.css">

    <style>
        .card-team {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 1rem;
        }
        .card-team:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .social-links a {
            font-size: 1.2rem;
            color: #555;
            transition: color 0.3s;
            text-decoration: none;
        }
        .social-links a:hover {
            color: #0d6efd;
        }
        .img-team {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 4px solid #f8f9fa;
        }
        .project-section {
            border: 1px solid rgba(0,0,0,0.05);
        }
    </style>
</head>
<body class="bg-light">
<?php include 'menu.php';  ?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Nossa Equipe</h2>
        <p class="text-muted">Conheça os desenvolvedores por trás deste projeto</p>
    </div>

    <div class="row g-4 mb-5"> <div class="col-12 col-md-6 col-lg-3">
            <div class="card card-team h-100 text-center p-3 shadow-sm">
                <div class="d-flex justify-content-center mt-3">
                    <img src="https://via.placeholder.com/150" class="rounded-circle img-team shadow-sm" alt="Foto Leo">
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bold">Leo</h5>
                    <p class="text-primary small mb-2">Desenvolvedor Full Stack</p>
                    <p class="card-text text-muted small">Sobre mim</p>
                    <div class="d-flex justify-content-center gap-3 social-links mt-3">
                        <a href="#" title="GitHub"><i class="bi bi-github"></i></a>
                        <a href="#" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        <a href="#" title="Currículo"><i class="bi bi-file-earmark-person-fill"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card card-team h-100 text-center p-3 shadow-sm">
                <div class="d-flex justify-content-center mt-3">
                    <img src="https://via.placeholder.com/150" class="rounded-circle img-team shadow-sm" alt="Foto Gabriel">
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bold">Gabriel</h5>
                    <p class="text-primary small mb-2">Desenvolvedor Full Stack</p>
                    <p class="card-text text-muted small">Sobre mim</p>
                    <div class="d-flex justify-content-center gap-3 social-links mt-3">
                        <a href="#" title="GitHub"><i class="bi bi-github"></i></a>
                        <a href="#" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        <a href="#" title="Currículo"><i class="bi bi-file-earmark-person-fill"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card card-team h-100 text-center p-3 shadow-sm">
                <div class="d-flex justify-content-center mt-3">
                    <img src="https://via.placeholder.com/150" class="rounded-circle img-team shadow-sm" alt="Foto Pedro">
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bold">Pedro</h5>
                    <p class="text-primary small mb-2">Desenvolvedor Full Stack</p>
                    <p class="card-text text-muted small">sobre mim</p>
                    <div class="d-flex justify-content-center gap-3 social-links mt-3">
                        <a href="#" title="GitHub"><i class="bi bi-github"></i></a>
                        <a href="#" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        <a href="#" title="Currículo"><i class="bi bi-file-earmark-person-fill"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card card-team h-100 text-center p-3 shadow-sm">
                <div class="d-flex justify-content-center mt-3">
                    <img src="https://via.placeholder.com/150" class="rounded-circle img-team shadow-sm" alt="Foto Kaue">
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bold">Kaue</h5>
                    <p class="text-primary small mb-2">Desenvolvedor Full Stack</p>
                    <p class="card-text text-muted small">sobre mim</p>
                    <div class="d-flex justify-content-center gap-3 social-links mt-3">
                        <a href="#" title="GitHub"><i class="bi bi-github"></i></a>
                        <a href="#" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        <a href="#" title="Currículo"><i class="bi bi-file-earmark-person-fill"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12"> 
            <div class="project-section bg-white p-4 p-md-5 rounded-4 shadow-sm mb-5">
                <div class="text-center mb-5">
                    <h3 class="fw-bold mb-4">Sobre o Projeto</h3>
                    <p class="lead text-muted mx-auto mb-4" style="max-width: 900px;">
                        Um site que fornece informações sobre linhas de ônibus, incluindo horários atualizados, rotas, pontos próximos e previsão de chegada. Ele permite acompanhar o deslocamento do ônibus no mapa e receber avisos sobre atrasos ou mudanças no itinerário, tornando o uso do transporte coletivo mais simples e confiável.
                    </p>
                    <a href="URL_DO_REPOSITORIO_GITHUB" target="_blank" class="btn btn-dark btn-lg px-5 shadow-sm">
                        <i class="bi bi-github me-2"></i>Ver Repositório no GitHub
                    </a>
                </div>

               <div id="carouselProfessores" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                    <div class="carousel-inner">

                        <div class="carousel-item active">
                            <div class="p-4 bg-light border-start border-primary border-5 rounded-3 shadow-sm">
                                <div class="row align-items-center">
                                    <div class="col-auto d-none d-md-block ps-4">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                            <i class="bi bi-mortarboard-fill fs-2"></i>
                                        </div>
                                    </div>
                                    <div class="col px-md-4">
                                        <h5 class="fw-bold mb-2">Agradecimento Especial</h5>
                                        <p class="text-secondary mb-0" style="line-height: 1.8; text-align: justify;">
                                            Gostaríamos de expressar nossa profunda gratidão ao nosso 
                                            <a href="LINK_DO_LINKEDIN_PROFESSOR_1" target="_blank" class="text-primary fw-bold text-decoration-none border-bottom border-primary border-2">
                                                Professor Anderson Iwanezuk <i class="bi bi-linkedin small"></i>
                                            </a>. 
                                            Seus ensinamentos, paciência e mentoria foram fundamentais para transformar linhas de código em uma solução real. Obrigado por nos desafiar a ir além e por compartilhar
                                            seu conhecimento com tanta dedicação.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="carousel-item">
                            <div class="p-4 bg-light border-start border-danger border-5 rounded-3 shadow-sm">
                                <div class="row align-items-center">
                                    <div class="col-auto d-none d-md-block ps-4">
                                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                            <i class="bi bi-mortarboard-fill fs-2"></i>
                                        </div>
                                    </div>
                                    <div class="col px-md-4">
                                        <h5 class="fw-bold mb-2">Agradecimento Especial</h5>
                                        <p class="text-secondary mb-0" style="line-height: 1.8; text-align: justify;">
                                            Gostaríamos de expressar nossa profunda gratidão ao nosso 
                                            <a href="LINK_DO_LINKEDIN_PROFESSOR_1" target="_blank" class="text-danger fw-bold text-decoration-none border-bottom border-danger border-2">
                                                Professor Anderson Iwanezuk <i class="bi bi-linkedin small"></i>
                                            </a>. 
                                            Seus ensinamentos, paciência e mentoria foram fundamentais para transformar linhas de código em uma solução real. Obrigado por nos desafiar a ir além e por compartilhar
                                            seu conhecimento com tanta dedicação.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="carousel-item">
                            <div class="p-4 bg-light border-start border-success border-5 rounded-3 shadow-sm">
                                <div class="row align-items-center">
                                    <div class="col-auto d-none d-md-block ps-4">
                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                            <i class="bi bi-mortarboard-fill fs-2"></i>
                                        </div>
                                    </div>
                                    <div class="col px-md-4">
                                        <h5 class="fw-bold mb-2">Agradecimento Especial</h5>
                                        <p class="text-secondary mb-0" style="line-height: 1.8; text-align: justify;">
                                            Gostaríamos de expressar nossa profunda gratidão ao nosso 
                                            <a href="LINK_DO_LINKEDIN_PROFESSOR_1" target="_blank" class="text-success fw-bold text-decoration-none border-bottom border-success border-2">
                                                Professor Anderson Iwanezuk <i class="bi bi-linkedin small"></i>
                                            </a>. 
                                            Seus ensinamentos, paciência e mentoria foram fundamentais para transformar linhas de código em uma solução real. Obrigado por nos desafiar a ir além e por compartilhar
                                            seu conhecimento com tanta dedicação.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="carousel-item">
                            <div class="p-4 bg-light border-start border-info border-5 rounded-3 shadow-sm">
                                <div class="row align-items-center">
                                    <div class="col-auto d-none d-md-block ps-4">
                                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                            <i class="bi bi-mortarboard-fill fs-2"></i>
                                        </div>
                                    </div>
                                    <div class="col px-md-4">
                                        <h5 class="fw-bold mb-2">Agradecimento Especial</h5>
                                        <p class="text-secondary mb-0" style="line-height: 1.8; text-align: justify;">
                                            Gostaríamos de expressar nossa profunda gratidão ao nosso 
                                            <a href="LINK_DO_LINKEDIN_PROFESSOR_1" target="_blank" class="text-info fw-bold text-decoration-none border-bottom border-info border-2">
                                                Professor Anderson Iwanezuk <i class="bi bi-linkedin small"></i>
                                            </a>. 
                                            Seus ensinamentos, paciência e mentoria foram fundamentais para transformar linhas de código em uma solução real. Obrigado por nos desafiar a ir além e por compartilhar
                                            seu conhecimento com tanta dedicação.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="carousel-item">
                            <div class="p-4 bg-light border-start border-warning border-5 rounded-3 shadow-sm">
                                <div class="row align-items-center">
                                    <div class="col-auto d-none d-md-block ps-4">
                                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                            <i class="bi bi-mortarboard-fill fs-2"></i>
                                        </div>
                                    </div>
                                    <div class="col px-md-4">
                                        <h5 class="fw-bold mb-2">Agradecimento Especial</h5>
                                        <p class="text-secondary mb-0" style="line-height: 1.8; text-align: justify;">
                                            Gostaríamos de expressar nossa profunda gratidão ao nosso 
                                            <a href="LINK_DO_LINKEDIN_PROFESSOR_1" target="_blank" class="text-warning fw-bold text-decoration-none border-bottom border-warning border-2">
                                                Professor Anderson Iwanezuk <i class="bi bi-linkedin small"></i>
                                            </a>. 
                                            Seus ensinamentos, paciência e mentoria foram fundamentais para transformar linhas de código em uma solução real. Obrigado por nos desafiar a ir além e por compartilhar
                                            seu conhecimento com tanta dedicação.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="carousel-item">
                            <div class="p-4 bg-light border-start border-dark border-5 rounded-3 shadow-sm">
                                <div class="row align-items-center">
                                    <div class="col-auto d-none d-md-block ps-4">
                                        <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                            <i class="bi bi-mortarboard-fill fs-2"></i>
                                        </div>
                                    </div>
                                    <div class="col px-md-4">
                                        <h5 class="fw-bold mb-2">Agradecimento Especial</h5>
                                        <p class="text-secondary mb-0" style="line-height: 1.8; text-align: justify;">
                                            Gostaríamos de expressar nossa profunda gratidão ao nosso 
                                            <a href="LINK_DO_LINKEDIN_PROFESSOR_1" target="_blank" class="text-dark fw-bold text-decoration-none border-bottom border-dark border-2">
                                                Professor Anderson Iwanezuk <i class="bi bi-linkedin small"></i>
                                            </a>. 
                                            Seus ensinamentos, paciência e mentoria foram fundamentais para transformar linhas de código em uma solução real. Obrigado por nos desafiar a ir além e por compartilhar
                                            seu conhecimento com tanta dedicação.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselProfessores" data-bs-slide="prev" style="width: 5%; filter: invert(1);">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselProfessores" data-bs-slide="next" style="width: 5%; filter: invert(1);">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Próximo</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'rodape.php'; ?>

<!-- Bootstrap JS -->
<script src="./js/bootstrap.bundle.min.js"></script>


</body>

</html>