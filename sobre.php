<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nós - Nossa Equipe</title>
    
    <!-- CSS específico -->
    <link rel="stylesheet" href="css/meu_estilo.css">

    <!-- Fonte local -->
    <link rel="stylesheet" href="css/fonts.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="css/bootstrap-icons.css">

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
                    <img src="https://media.licdn.com/dms/image/v2/D4E35AQHnJlTa96dOWQ/profile-framedphoto-shrink_200_200/B4EZyRvOssGYAY-/0/1771971603492?e=1772578800&v=beta&t=AuBMxCkzDsiq7jJQ4PQ97eSig9ZkNY43escAJSzR0AA" class="rounded-circle img-team shadow-sm" alt="Foto Leo">
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bold">Leo</h5>
                    <p class="text-primary small mb-2">Desenvolvedor Full Stack</p>
                    <p class="card-text text-muted small">Desenvolvedor Web em Itapetininga, SP. Focado em PHP, JavaScript e bancos de dados, unindo tecnologia à utilidade pública.</p>
                    <div class="d-flex justify-content-center gap-3 social-links mt-3">
                        <a href="https://github.com/leo0liv" title="GitHub"><i class="bi bi-github"></i></a>
                        <a href="https://www.linkedin.com/in/leonardo-jos%C3%A9-05646a338/" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        <a href="#" title="Currículo"><i class="bi bi-file-earmark-person-fill"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card card-team h-100 text-center p-3 shadow-sm">
                <div class="d-flex justify-content-center mt-3">
                    <img src="img/Gabriel.jpg" class="rounded-circle img-team shadow-sm" alt="Foto Gabriel">
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bold">Gabriel</h5>
                    <p class="text-primary small mb-2">Desenvolvedor Full Stack</p>
                    <p class="card-text text-muted small">Desenvolvedor Web em Itapetininga, SP. Focado em Bootstrap, JavaScript basico, C# e bancos de dados</p>
                    <div class="d-flex justify-content-center gap-3 social-links mt-3">
                        <a href="https://github.com/saponoel" title="GitHub"><i class="bi bi-github"></i></a>
                        <a href="https://www.youtube.com/@sapponoel" title="Youtube"><i class="bi bi-youtube"></i></a>
                        <a href="#" title="Currículo"><i class="bi bi-file-earmark-person-fill"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card card-team h-100 text-center p-3 shadow-sm">
                <div class="d-flex justify-content-center mt-3">
                    <img src="img/Zene.jpg" class="rounded-circle img-team shadow-sm" alt="Foto Pedro">
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bold">Pedro Zene</h5>
                    <p class="text-primary small mb-2">Desenvolvedor Full Stack</p>
                    <p class="card-text text-muted small">Desenvolvedor de junior html , php avançado, JavaScript basico, SCSS</p>
                    <div class="d-flex justify-content-center gap-3 social-links mt-3">
                        <a href="https://github.com/pdrfxptrem" title="GitHub"><i class="bi bi-github"></i></a>
                        <a href="#" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        <a href="#" title="Currículo"><i class="bi bi-file-earmark-person-fill"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card card-team h-100 text-center p-3 shadow-sm">
                <div class="d-flex justify-content-center mt-3">
                    <img src="img/Kaue.jpg" class="rounded-circle img-team shadow-sm" alt="Foto Kaue">
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bold">Kaue</h5>
                    <p class="text-primary small mb-2">Desenvolvedor Full Stack</p>
                    <p class="card-text text-muted small">Desenvolvedor junior html, php Basico JavaScript Basico, Scss Bootstrap, muito foco </p>
                    <div class="d-flex justify-content-center gap-3 social-links mt-3">
                        <a href="https://github.com/kaue123475" title="GitHub"><i class="bi bi-github"></i></a>
                        <a href="#" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        <a href="#" title="Currículo"><i class="bi bi-file-earmark-person-fill"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12"> 
            <div class=" card project-section bg-white p-4 p-md-5 rounded-4 shadow-sm mb-5">
                <div class="text-center mb-5">
                    <h3 class="fw-bold mb-4">Sobre o Projeto</h3>
                    <p class="lead text-muted mx-auto mb-4" style="max-width: 900px;">
                        Um site que fornece informações sobre linhas de ônibus, incluindo horários atualizados, rotas, pontos próximos e previsão de chegada.
                    </p>
                    <a href="https://github.com/leo0liv/ProjetoTransportePublico" target="_blank" class="btn btn-dark btn-lg px-5 shadow-sm">
                        <i class="bi bi-github me-2"></i>Ver Repositório no GitHub
                    </a>
                </div>

                <div id="carouselProfessores" class="carousel slide px-md-5" data-bs-ride="carousel" data-bs-interval="5000">
                    <div class="carousel-inner">
                        
                        <div class="carousel-item active">
                            <div class="p-4 bg-light border-start border-primary border-5 rounded-3 shadow-sm mx-md-4">
                                <div class="row align-items-center">
                                    <div class="col-auto d-none d-md-block ps-4">
                                        <img src="https://media.licdn.com/dms/image/v2/C4D03AQECSX4tvUHbZA/profile-displayphoto-shrink_800_800/profile-displayphoto-shrink_800_800/0/1517468919230?e=1773273600&v=beta&t=DysTKlNyq1I0Tc2pvlcNK9o5ege3HnS1umxXK_m66bw" alt="Foto do Professor" class="rounded-circle shadow-sm object-fit-cover" style="width: 80px; height: 80px; border: 3px solid #0d6efd;">
                                    </div>
                                    <div class="col px-md-4">
                                        <h5 class="fw-bold mb-2">Agradecimento Especial</h5>
                                        <p class="text-secondary mb-0" style="line-height: 1.8; text-align: justify;">
                                            Gostaríamos de expressar nossa profunda gratidão ao nosso 
                                            <a href="https://www.linkedin.com/in/iwanezuk/" target="_blank" class="text-primary fw-bold text-decoration-none border-bottom border-primary border-2">
                                                Professor Anderson Iwanezuk <i class="bi bi-linkedin small"></i>
                                            </a>. Seus ensinamentos e mentoria foram fundamentais para transformar código em solução real.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="carousel-item">
                            <div class="p-4 bg-light border-start border-secondary border-5 rounded-3 shadow-sm mx-md-4">
                                <div class="row align-items-center">
                                    <div class="col-auto d-none d-md-block ps-4">
                                        <img src="https://media.licdn.com/dms/image/v2/D4D03AQGvqqu4NNI_Xw/profile-displayphoto-crop_800_800/B4DZkLZG1oG8AM-/0/1756832755722?e=1773273600&v=beta&t=ViguXCGYu7Rw7u_j5fwx5uyUrK3KESgssp5p28rFuZU" alt="Foto do Professor" class="rounded-circle shadow-sm object-fit-cover" style="width: 80px; height: 80px; border: 3px solid #6c757d;">
                                    </div>
                                    <div class="col px-md-4">
                                        <h5 class="fw-bold mb-2">Agradecimento Especial</h5>
                                        <p class="text-secondary mb-0" style="line-height: 1.8; text-align: justify;">
                                            Gostaríamos de expressar nossa profunda gratidão ao nosso 
                                            <a href="https://www.linkedin.com/in/giovanitoreli/" target="_blank" class="text-secondary fw-bold text-decoration-none border-bottom border-secondary border-2">
                                                Professor Giovani Toreli <i class="bi bi-linkedin small"></i>
                                            </a>. Obrigado por compartilhar seu conhecimento com tanta dedicação.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="carousel-item">
                            <div class="p-4 bg-light border-start border-danger border-5 rounded-3 shadow-sm mx-md-4">
                                <div class="row align-items-center">
                                    <div class="col-auto d-none d-md-block ps-4">
                                        <img src="https://media.licdn.com/dms/image/v2/C4D03AQFXqSayGRs7BQ/profile-displayphoto-shrink_800_800/profile-displayphoto-shrink_800_800/0/1576897161984?e=1773273600&v=beta&t=OIqwEKE42ksxbNO7Dd0qgDHXgVqB-w_BXKV_eGkMCgA" alt="Foto do Professor" class="rounded-circle shadow-sm object-fit-cover" style="width: 80px; height: 80px; border: 3px solid #dc3545;">
                                    </div>
                                    <div class="col px-md-4">
                                        <h5 class="fw-bold mb-2">Agradecimento Especial</h5>
                                        <p class="text-secondary mb-0" style="line-height: 1.8; text-align: justify;">
                                            Gostaríamos de expressar nossa profunda gratidão ao nosso 
                                            <a href="https://www.linkedin.com/in/elielter-de-ara%C3%BAjo-welter-46955219a/" target="_blank" class="text-danger fw-bold text-decoration-none border-bottom border-danger border-2">
                                                Professor Elielter de Araújo <i class="bi bi-linkedin small"></i>
                                            </a>. Obrigado por compartilhar seu conhecimento com tanta dedicação.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="carousel-item">
                            <div class="p-4 bg-light border-start border-success border-5 rounded-3 shadow-sm mx-md-4">
                                <div class="row align-items-center">
                                    <div class="col-auto d-none d-md-block ps-4">
                                        <img src="https://media.licdn.com/dms/image/v2/D4E03AQH1T2Qsc6j26A/profile-displayphoto-shrink_800_800/B4EZbEpZOuHIAs-/0/1747055908597?e=1773273600&v=beta&t=KmhqDc4Cd-y0EIMO4NIzyinHjhEpDYL7Qds7NCFevTI" alt="Foto do Professor" class="rounded-circle shadow-sm object-fit-cover" style="width: 80px; height: 80px; border: 3px solid #198754;">
                                    </div>
                                    <div class="col px-md-4">
                                        <h5 class="fw-bold mb-2">Agradecimento Especial</h5>
                                        <p class="text-secondary mb-0" style="line-height: 1.8; text-align: justify;">
                                            Gostaríamos de expressar nossa profunda gratidão ao nosso 
                                            <a href="https://www.linkedin.com/in/antonio-corr%C3%AAa-a0850791/" target="_blank" class="text-success fw-bold text-decoration-none border-bottom border-success border-2">
                                                Professor Antonio Corrêa <i class="bi bi-linkedin small"></i>
                                            </a>. Obrigado por compartilhar seu conhecimento com tanta dedicação.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="carousel-item">
                            <div class="p-4 bg-light border-start border-info border-5 rounded-3 shadow-sm mx-md-4">
                                <div class="row align-items-center">
                                    <div class="col-auto d-none d-md-block ps-4">
                                        <img src="https://media.licdn.com/dms/image/v2/D4D03AQGK-kFJ3HxYBw/profile-displayphoto-crop_800_800/B4DZq9tqmpJAAI-/0/1764119458108?e=1773273600&v=beta&t=Y4xpLheT3G2fHaLhhjw7yKVEtoEBJhy_DIysUHYat98" alt="Foto do Professor" class="rounded-circle shadow-sm object-fit-cover" style="width: 80px; height: 80px; border: 3px solid #0dcaf0;">
                                    </div>
                                    <div class="col px-md-4">
                                        <h5 class="fw-bold mb-2">Agradecimento Especial</h5>
                                        <p class="text-secondary mb-0" style="line-height: 1.8; text-align: justify;">
                                            Gostaríamos de expressar nossa profunda gratidão ao nosso 
                                            <a href="https://www.linkedin.com/in/leandromaschietto/" target="_blank" class="text-info fw-bold text-decoration-none border-bottom border-info border-2">
                                                Professor Leandro Maschietto  <i class="bi bi-linkedin small"></i>
                                            </a>. Obrigado por compartilhar seu conhecimento com tanta dedicação.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="carousel-item">
                            <div class="p-4 bg-light border-start border-warning border-5 rounded-3 shadow-sm mx-md-4">
                                <div class="row align-items-center">
                                    <div class="col-auto d-none d-md-block ps-4">
                                        <img src="https://media.licdn.com/dms/image/v2/D4D0BAQF9XPvhlkoORA/company-logo_200_200/company-logo_200_200/0/1735823078140/senacsaopaulo_logo?e=1773273600&v=beta&t=XT-OHQPxJWvzBFxNOIilIzSxrQdhAoE4vuromLIppso" alt="Foto do Professor" class="rounded-circle shadow-sm object-fit-cover" style="width: 80px; height: 80px; border: 3px solid #ffc107;">
                                    </div>
                                    <div class="col px-md-4">
                                        <h5 class="fw-bold mb-2">Agradecimento Especial</h5>
                                        <p class="text-secondary mb-0" style="line-height: 1.8; text-align: justify;">
                                            Gostaríamos de expressar nossa profunda gratidão ao 
                                            <a href="https://www.linkedin.com/school/senacsaopaulo/" target="_blank" class="text-warning fw-bold text-decoration-none border-bottom border-warning border-2">
                                                Senac São Paulo <i class="bi bi-linkedin small"></i>
                                            </a>. 
                                            Obrigado por proporcionar um ambiente de excelência e por todo o suporte oferecido durante nossa jornada de aprendizado.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselProfessores" data-bs-slide="prev" style="width: 40px; filter: invert(1);">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselProfessores" data-bs-slide="next" style="width: 40px; filter: invert(1);">
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
<script src="js/bootstrap.bundle.min.js"></script>


</body>

</html>