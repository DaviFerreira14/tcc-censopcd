<?php
session_start();

$data = json_decode(file_get_contents("php://input"), true);
if (isset($data['tema'])) {
    $_SESSION['tema'] = $data['tema']; // Salvar tema na sessão
    echo json_encode(['success' => true, 'tema' => $_SESSION['tema']]);
} else {
    http_response_code(400); // Define o código de resposta HTTP para "Bad Request"
    echo json_encode(['error' => 'Tema não especificado.']);
}
?>