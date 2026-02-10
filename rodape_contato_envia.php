<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Após 15 segundos a página sera redirecionada para index.php -->
    <meta http-equiv="refresh" content="15;URL=index.php">
    <title>Verificação do Contato</title>
</head>
<body class="fundofixo">
<?php include('menu.php'); ?>
<main class="container">
    <section>
        <div class="jumbotron alert-danger">
            <h1>Agradecemos seu contato!</h1>
            <?php
                $destino        =   "contato@transportepúblico.com.br";
                $nome_contato   =   $_POST['nome_contato'];
                $email_contato  =   $_POST['email_contato'];
                $msg_contato    =   "Mensagem de:".$nome_contato."\n".$_POST['comentarios_contato'];
                $mailsend   =   mail($destino,"Formulário de comentário",$msg_contato,"From:",$email_contato);
            ?>
            <div class="text-center">
                <p>
                    Obrigado por enviar seus comentários,
                    <b><?php echo $nome_contato; ?></b>!
                </p>
                <p>Mensagem enviada com sucesso!</p>
                <h5>
                    Caso não visualize a mensagem de agradecimento,
                    entre em contato através do email
                    <br>
                    <b><i><?php echo $destino; ?></i></b>
                </h5>
            </div> <!-- fecha text-center -->
        </div> <!-- fecha jumbotron -->

    </section>
</main>
<!-- rodapé -->
<footer>
    <?php include('rodape.php'); ?>
</footer>

</body>
</html>