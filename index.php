<?php

header('Content-Type: application/json');

$input = file_get_contents("php://input");

error_log("JSON: " . $input);

if (!$input) {
    http_response_code(400);
    echo json_encode([ "estado" => "error", "mensaje" => "JSON inválido" ]);
    exit;
}
else{
    echo json_encode([
    "estado" => "ok"
]);
}

$host = getenv('MYSQLHOST');
$port = getenv('MYSQLPORT');
$dbname = getenv('MYSQLDATABASE');
$user = getenv('MYSQLUSER');
$password = getenv('MYSQLPASSWORD');

$nombre = trim($input['nombre'] ?? '');
$puntaje = intval($input['intentos'] ?? 0);
$fecha = trim($input['fecha_hora'] ?? '');

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",$user,$password);

    $sql = "INSERT INTO records (nombre, puntaje, fecha) VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([ $nombre, $puntaje, $fecha ]);
} 
    
catch (PDOException $e) {

    error_log("Error BD: " . $e->getMessage());
    http_response_code(500);

    echo json_encode([
        "estado" => "error",
        "mensaje" => $e->getMessage()
    ]);
}

?>


