<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'decorajlp';

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die(json_encode([]));
}

$sql = "SELECT id, nome, preco, img, descr FROM produtos";
$result = $conn->query($sql);

$produtos = [];
while($row = $result->fetch_assoc()) {
    $produtos[] = $row;
}

echo json_encode($produtos);
$conn->close();

?>
