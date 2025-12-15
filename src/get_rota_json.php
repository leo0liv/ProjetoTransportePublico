<?php
// Define que o conteúdo retornado é JSON
header('Content-Type: application/json');

// ----------------------------------------------------
// 1. CONFIGURAÇÕES DO BANCO DE DADOS
// Mantenha as credenciais seguras!
// ----------------------------------------------------
$servername = "localhost";
$username = "TransportePublico_ti19"; // Seu usuário
$password = "senacti19";             // Sua senha
$dbname = "TransportePublico_ti19";   // Seu banco de dados
$id_linha_desejada = 1; // ID da Linha '101-A' que queremos mapear

// ----------------------------------------------------
// 2. CONEXÃO COM O BANCO DE DADOS
// ----------------------------------------------------
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    // Se falhar, retorna um erro JSON
    http_response_code(500); // Código de erro do servidor
    die(json_encode(array("error" => "Conexão falhou: " . $conn->connect_error)));
}

// Define o charset para garantir acentuação correta
$conn->set_charset("utf8");

// ----------------------------------------------------
// 3. CONSULTA SQL
// Puxa as coordenadas e nome dos pontos ordenados
// ----------------------------------------------------
$sql = "
    SELECT 
        p.latitude, 
        p.longitude, 
        p.nome AS nome_ponto
    FROM 
        tbrotas AS r
    JOIN 
        tbpontos AS p ON r.id_ponto = p.id_ponto
    WHERE 
        r.id_linha = ? 
    ORDER BY 
        r.ordem ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_linha_desejada); // "i" indica que é um integer
$stmt->execute();
$result = $stmt->get_result();

// ----------------------------------------------------
// 4. PROCESSAMENTO DOS RESULTADOS
// ----------------------------------------------------
$dados_rota = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Adiciona cada linha como um objeto no array
        $dados_rota[] = $row;
    }
} else {
    // Se não houver resultados, retorna um array vazio
    $dados_rota = array("message" => "Nenhum ponto encontrado para esta linha.");
}

// ----------------------------------------------------
// 5. CONVERSÃO PARA JSON E IMPRESSÃO
// ----------------------------------------------------
echo json_encode($dados_rota, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);

// JSON_NUMERIC_CHECK: Garante que os valores DECIMAL (Latitude/Longitude)
// sejam formatados como números e não strings no JSON.
// JSON_PRETTY_PRINT: (Opcional) Formata o JSON com quebras de linha para facilitar a leitura.

$conn->close();

?>