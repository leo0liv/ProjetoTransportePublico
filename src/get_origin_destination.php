<?php
// Define o cabeçalho para garantir o retorno JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 

// --- 1. CONFIGURAÇÕES DE CONEXÃO ---
$servername = "localhost";
$username = "TransportePublico_ti19"; 
$password = "senacti19"; 
$dbname = "TransportePublico_ti19";

$response = ['success' => false, 'origin' => null, 'destination' => null];

try {
    // --- 2. CONEXÃO E OBTENÇÃO DO ID ---
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Falha na conexão: " . $conn->connect_error);
    }
    $id_linha = isset($_GET['id']) ? intval($_GET['id']) : 0; 

    // --- 3. BUSCA DOS PONTOS DE INÍCIO E FIM ---
    // Encontrar a ORDEM mínima (início) e máxima (fim) da rota.
    $sql = "SELECT p.latitude, p.longitude
            FROM tbrotas r
            JOIN tbpontos p ON r.id_ponto = p.id_ponto
            WHERE r.id_linha = ?
            ORDER BY r.ordem ASC"; // Ordena para garantir que o primeiro resultado é a origem
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_linha);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $points = [];
    while($row = $result->fetch_assoc()) {
        $points[] = [
            'lat' => (float)$row['latitude'], 
            'lng' => (float)$row['longitude']
        ];
    }
    
    if (count($points) >= 2) {
        $response['success'] = true;
        // O primeiro ponto é a Origem
        $response['origin'] = $points[0];
        // O último ponto é o Destino
        $response['destination'] = end($points); 
    } else {
        throw new Exception("Linha encontrada, mas com menos de dois pontos de rota.");
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
    echo json_encode($response);
}
?>