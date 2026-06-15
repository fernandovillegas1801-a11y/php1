<?php



header('Content-Type: application/json');

// Leer JSON recibido
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode([
        "estado" => "error",
        "mensaje" => "JSON inválido"
    ]);
    exit;
}

$nombre = trim($data['nombre'] ?? '');
$puntaje = intval($data['intentos'] ?? 0);
$fecha = trim($data['fecha_hora'] ?? '');








$host = getenv('RAILWAY_TCP_PROXY_DOMAIN');
$port = getenv('RAILWAY_TCP_PROXY_PORT');
$dbname = getenv('MYSQLDATABASE');
$user = getenv('MYSQLUSER');
$password = getenv('MYSQLPASSWORD');

try {

    $conn = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $user,
        $password
    );

    $conn->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $sql = "INSERT INTO records
        (nombre, puntaje, fecha)
        VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param($nombre, puntaje, $fecha);
$stmt->execute();
    

} catch(PDOException $e) {

    die($e->getMessage());
}




/*
header('Content-Type: application/json');

// Leer JSON recibido
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode([
        "estado" => "error",
        "mensaje" => "JSON inválido"
    ]);
    exit;
}

$nombre = trim($data['nombre'] ?? '');
$puntaje = intval($data['intentos'] ?? 0);
$fecha = trim($data['fecha_hora'] ?? '');

if ($nombre == '' || $fecha == '') {
    http_response_code(400);
    echo json_encode([
        "estado" => "error",
        "mensaje" => "Datos incompletos"
    ]);
    exit;
}

// Variables de entorno Railway
$host = getenv('MYSQLHOST');
$port = getenv('MYSQLPORT');
$dbname = getenv('MYSQLDATABASE');
$user = getenv('MYSQLUSER');
$password = getenv('MYSQLPASSWORD');

try {

    $conexion = new mysqli(
        $host,
        $user,
        $password,
        $dbname,
        $port
    );

    if ($conexion->connect_error) {
        throw new Exception($conexion->connect_error);
    }

    $sql = "INSERT INTO records
            (nombre, puntaje, fecha)
            VALUES (?, ?, ?)";

    $stmt = $conexion->prepare($sql);

    $stmt->bind_param(
        "sis",
        $nombre,
        $puntaje,
        $fecha
    );

    $stmt->execute();

    echo json_encode([
        "estado" => "ok",
        "mensaje" => "Registro almacenado",
        "id" => $conexion->insert_id
    ]);

    $stmt->close();
    $conexion->close();

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        "estado" => "error",
        "mensaje" => $e->getMessage()
    ]);
    
}
*/

?>
