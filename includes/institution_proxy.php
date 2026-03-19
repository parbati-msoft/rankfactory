<?php
// includes/institution_proxy.php

// 1. Prevent any accidental output before JSON
ob_start();

// 2. Include api.php (same folder)
require_once __DIR__ . '/api.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!empty($input['name'])) {
    $response = callApi(URL_INSTITUTIONS, 'POST', [
        'name'      => $input['name'],
        'address'   => $input['address'] ?? '',
        'is_active' => true
    ]);

    // 3. Wipe the buffer (just in case)
    if (ob_get_length()) ob_clean();

    header('Content-Type: application/json');
    echo json_encode($response['body']);
    exit;
}
