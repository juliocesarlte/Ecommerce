<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'decorajlp';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["sucesso" => false]));
}

$usuario_id = $data['usuario_id'];
$total = $data['total'];

// A data é inserida automaticamente pelo banco ou usamos NOW()
$stmt = $conn->prepare("INSERT INTO pedidos (usuario_id, total, data_pedido) VALUES (?, ?, NOW())");
$stmt->bind_param("id", $usuario_id, $total);

if ($stmt->execute()) {
    echo json_encode(["sucesso" => true, "pedido_id" => $stmt->insert_id]);
} else {
    echo json_encode(["sucesso" => false, "msg" => $conn->error]);
}

$conn->close();
?>