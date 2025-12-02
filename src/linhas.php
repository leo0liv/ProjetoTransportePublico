<?php
// Inclui o arquivo de conexão
include '../connections/db_connect.php'; 

// Consulta
$sql = "SELECT id_linha, codigo, nome FROM tblinhas ORDER BY codigo";
$resultado = $conn->query($sql);

// Fecha a conexão
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linha - publico</title>

    <!-- CSS específico -->
    <link rel="stylesheet" href="../css/meu_estilo.css">

    <!-- Fonte local -->
    <link rel="stylesheet" href="../css/fonts.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../css/bootstrap.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="../css/bootstrap-icons.css">
</head>
<body>
    <main class="container mt-4">
        <h1><i class="bi bi-bus-front-fill"></i> Linhas de Ônibus Cadastradas</h1>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Código</th>
                    <th>Nome da Linha</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultado->num_rows > 0) {
                    while($linha = $resultado->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $linha["codigo"]. "</td>";
                        echo "<td>" . $linha["nome"]. "</td>";
                        echo "<td><a href='detalhes_linha.php?id=" . $linha["id_linha"] . "' class='btn btn-sm btn-info'>Ver Rota</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Nenhuma linha encontrada.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </main>
    

    <!-- Bootstrap JS -->
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>