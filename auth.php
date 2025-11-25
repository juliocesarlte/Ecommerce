<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'decorajlp';

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die(json_encode(["sucesso" => false, "msg" => "Erro banco"]));
}

$acao = $data['acao'];

if ($acao == 'cadastro') {
    $nome = $data['nome'];
    $email = $data['email'];
    $cpf = $data['cpf'];
    $senha = password_hash($data['senha'], PASSWORD_DEFAULT); 

    $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if($check->get_result()->num_rows > 0){
        echo json_encode(["sucesso" => false, "msg" => "E-mail já cadastrado"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, cpf) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nome, $email, $senha, $cpf);

    if ($stmt->execute()) {
        $id = $stmt->insert_id;
        echo json_encode(["sucesso" => true, "usuario" => ["id" => $id, "nome" => $nome]]);
    } else {
        echo json_encode(["sucesso" => false, "msg" => "Erro ao cadastrar"]);
    }
} 
else if ($acao == 'login') {
    $email = $data['email'];
    $senha = $data['senha'];

    $stmt = $conn->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($senha, $row['senha'])) {
            echo json_encode(["sucesso" => true, "usuario" => ["id" => $row['id'], "nome" => $row['nome']]]);
        } else {
            echo json_encode(["sucesso" => false, "msg" => "Senha incorreta"]);
        }
    } else {
        echo json_encode(["sucesso" => false, "msg" => "Usuário não encontrado"]);
    }
}

$conn->close();

?>
