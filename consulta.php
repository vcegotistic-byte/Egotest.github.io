<?php
header('Content-Type: application/json');

if (!isset($_GET['dni']) || !preg_match('/^\d{8}$/', $_GET['dni'])) {
    echo json_encode(['success' => false, 'message' => 'DNI inválido o no proporcionado']);
    exit;
}

$dni = $_GET['dni'];
$token = "sk_9338.PjzSpGbM9neFKOnuoNEKrbQnhMZPhH6b";

// 🔸 Usamos la versión HTTP de la API
$url = "http://api.decolecta.com/v1/reniec/dni?numero=" . $dni;

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $token",
        "User-Agent: decolecta-client"
    ],
    // Opcional si hay problemas de conexión local
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo json_encode(['success' => false, 'message' => 'Error en la consulta: ' . $err]);
    exit;
}

$data = json_decode($response, true);

if ($data === null) {
    echo json_encode(['success' => false, 'message' => 'Respuesta no válida de la API']);
    exit;
}

if (isset($data['first_name'])) {
    $nombreCompleto = trim($data['first_last_name'] . ' ' . $data['second_last_name'] . ' ' . $data['first_name']);
    echo json_encode([
        'success' => true,
        'nombre_completo' => $nombreCompleto
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'DNI no encontrado o inválido'
    ]);
}