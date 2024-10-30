<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não está logado.']);
    exit();
}

// Incluir o arquivo de conexão com o banco de dados
include 'db.php';

$usuario_id = $_SESSION['usuario_id'];
$logradouro = $_POST['logradouro'] ?? '';
$bairro = $_POST['bairro'] ?? '';
$cidade = $_POST['cidade'] ?? '';
$estado = $_POST['estado'] ?? '';
$cep = $_POST['cep'] ?? ''; // Captura o CEP

// Verifica se todos os campos estão preenchidos
if (empty($logradouro) || empty($bairro) || empty($cidade) || empty($estado) || empty($cep)) {
    echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios.']);
    exit();
}

// Atualiza o endereço no banco de dados
$stmt = $conn->prepare("UPDATE enderecos SET logradouro = ?, bairro = ?, cidade = ?, estado = ?, cep = ? WHERE usuario_id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro na preparação da declaração: ' . $conn->error]);
    exit();
}

$stmt->bind_param("sssssi", $logradouro, $bairro, $cidade, $estado, $cep, $usuario_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Endereço atualizado com sucesso.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar o endereço: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>