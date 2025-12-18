<?php
header('Content-Type: application/json');
require_once '../connections/db_connect.php';

$action = $_GET['action'] ?? '';

if ($action == 'get_linhas') {
    $query = "SELECT l.id_linha, l.nome AS linha_nome, l.codigo, p.nome AS ponto_nome, 
                     p.latitude, p.longitude, r.ordem
              FROM tblinhas l
              JOIN tbrotas r ON l.id_linha = r.id_linha
              JOIN tbpontos p ON r.id_ponto = p.id_ponto
              ORDER BY l.id_linha, r.ordem";
    
    $result = $conn->query($query);
    $linhas = [];

    while ($row = $result->fetch_assoc()) {
        $id = $row['id_linha'];
        if (!isset($linhas[$id])) {
            $linhas[$id] = [
                'id' => $id, 
                'nome' => $row['codigo'] . " - " . $row['linha_nome'], 
                'pontos' => []
            ];
        }
        $linhas[$id]['pontos'][] = [
            'nome' => $row['ponto_nome'], 
            'lat' => (float)$row['latitude'], 
            'lng' => (float)$row['longitude']
        ];
    }
    echo json_encode(array_values($linhas));

} elseif ($action == 'get_veiculos') {
    $id_linha = (int)($_GET['id_linha'] ?? 0);
    
    $query = "SELECT v.placa, tr.latitude, tr.longitude, tr.velocidade 
              FROM tblocalizacao_tempo_real tr
              JOIN tbveiculos v ON tr.id_veiculo = v.id_veiculo
              WHERE v.id_linha = $id_linha AND tr.id_localizacao IN 
              (SELECT MAX(id_localizacao) FROM tblocalizacao_tempo_real GROUP BY id_veiculo)";
    
    $result = $conn->query($query);
    $veiculos = [];
    while ($row = $result->fetch_assoc()) {
        $veiculos[] = $row;
    }
    echo json_encode($veiculos);
}
?>