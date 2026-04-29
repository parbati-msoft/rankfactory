<?php
// ajax-handler.php
require_once __DIR__ . '/includes/api.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);

switch ($action) {
    case 'mini-profile':
        $res = callApi(URL_MINI_PROFILE, 'GET');
        echo json_encode($res['body'] ?? []);
        break;

    case 'register-cee':
        $res = callApi(URL_REGISTER_STUDENT, 'POST', [
            'name' => $input['name'],
            'password' => $input['password'],
            'email' => $input['email'],
            'contact' => $input['contact'],
            'district' => $input['district'],
            'role' => 'Student'
        ]);
        if ($res['code'] === 201 || $res['code'] === 200) {
            // Extract the token from the body
            $token = $res['body']['access_token'] ?? null;

            if ($token) {
                $_SESSION['rf_token'] = $token; // Save token to session
            }

            echo json_encode([
                'success' => true,
                'is_existing' => $res['body']['is_existing'] ?? false
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $res['body']['message'] ?? 'Registration failed'
            ]);
        }
        break;

    case 'login-cee':
        $credentials = [
            'username' => trim($input['identity'] ?? ''), // email or phone
            'password' => $input['password'] ?? ''
        ];

        // Call your RankFactory Login Endpoint
        $res = callApi(URL_LOGIN_STUDENT, 'POST', $credentials);

        if ($res['code'] === 200) {
            // If the API returns a token, save it to the session
            $token = $res['body']['token'] ?? $res['body']['access_token'] ?? $res['body']['data']['token'] ?? null;

            if ($token) {
                $_SESSION['rf_token'] = $token; // Save it for the next call
                echo json_encode(['success' => true]);
            } else {
                // If no token found, the API might still be using cookies 
                // but let's log this for debugging
                echo json_encode(['success' => true, 'debug' => 'No token found in response']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        }
        exit;

    case 'get-batch-details':
        $url = ($_GET['type'] === 'online') ? URL_ONLINE_BATCH_DETAIL : URL_PHYSICAL_BATCH_DETAIL;
        $res = callApi($url, 'GET');
        echo json_encode($res['body'] ?? []);
        break;

    case 'process-cee-booking':
        if (empty($_POST)) {
            $json = file_get_contents('php://input');
            $input = json_decode($json, true);
        } else {
            $input = $_POST;
        }

        // --- 1. Handle Online Booking ---
        $onlineId = $input['online_batch_id'] ?? $input['batch_id'] ?? null;
        $physicalId = $input['physical_class_id'] ?? $input['physical_id'] ?? null;
        $totalPaid = floatval($input['amount'] ?? 0);

        $p1 = floatval($input['online_price'] ?? 0);
        $p2 = floatval($input['physical_price'] ?? 0);

        $onlineAmount = 0;
        $physicalAmount = 0;

        if ($onlineId && $physicalId) {
            $totalPrice = $p1 + $p2;

            if ($totalPaid >= $totalPrice && $totalPrice > 0) {
                // Full payment logic
                $onlineAmount = $p1;
                $physicalAmount = $p2;
            } elseif ($totalPrice > 0) {
                // Proportional split logic
                $onlineAmount = round(($p1 / $totalPrice) * $totalPaid, 2);
                $physicalAmount = round(($p2 / $totalPrice) * $totalPaid, 2);
            }
        } elseif ($onlineId) {
            $onlineAmount = $totalPaid;
        } elseif ($physicalId) {
            $physicalAmount = $totalPaid;
        }

        $responses = [];
        $hasSuccess = false;

        if (!empty($onlineId)) {
            $onlinePayload = [
                'batch_id'       => $onlineId,
                'amount'         => $onlineAmount,
                'method'         => $input['method'] ?? '',
                'payment_mode'   => $input['payment_mode'] ?? 'direct',
                'transaction_id' => $input['transaction_id'] ?? '',
            ];

            if (!empty($_FILES['screenshot']['tmp_name'])) {
                $onlinePayload['screenshot'] = new CURLFile($_FILES['screenshot']['tmp_name'], $_FILES['screenshot']['type'], $_FILES['screenshot']['name']);
            }

            $onlineRes = callApi(URL_ONLINE_COURSE_BOOKING, 'POST', $onlinePayload);
            $responses['online'] = $onlineRes['body'];
            if ($onlineRes['body']['success'] ?? false) $hasSuccess = true;
        }

        // --- 2. Handle Physical Booking ---
        if (!empty($physicalId)) {
            $physicalPayload = [
                'class_id'         => $physicalId,
                'verificationMode' => ($input['method'] === 'direct') ? 'Esewa_Direct' : ($input['payment_mode'] ?? 'Manual'),
                'paymentAmount'    => $physicalAmount,
                'bill_no'          => $input['transaction_id'] ?? '',
                'message'          => 'CEE Split Payment. Allocated: Rs. $physicalAmount',
            ];

            if (!empty($_FILES['screenshot']['tmp_name'])) {
                $physicalPayload['payslip'] = new CURLFile($_FILES['screenshot']['tmp_name'], $_FILES['screenshot']['type'], $_FILES['screenshot']['name']);
            }

            $physicalRes = callApi(URL_PHYSICAL_BOOKING_STORE, 'POST', $physicalPayload);
            $responses['physical'] = $physicalRes['body'];
            if ($physicalRes['body']['success'] ?? false) $hasSuccess = true;
        }

        // Return combined result
        echo json_encode([
            'success' => $hasSuccess,
            'details' => $responses
        ]);
        exit;


    case 'prepare-esewa':
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        $product_code = $_ENV['ESEWA_PRODUCT_CODE'] ?? 'EPAYTEST';
        $secret_key = $_ENV['ESEWA_SECRET_KEY'] ?? '';

        if (empty($secret_key)) {
            echo json_encode(['success' => false, 'error' => 'Secret Key not found in .env']);
            exit;
        }

        $amount = $input['amount'] ?? 0;
        $batch_id = $input['batch_id'] ?? 0;      // Online ID
        $physical_id = $input['physical_id'] ?? 0; // Physical ID
        $online_price = $input['online_price'] ?? 0;
        $physical_price = $input['physical_price'] ?? 0;

        $isLocal = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']) || $_SERVER['HTTP_HOST'] === 'localhost';

        $basePath = $isLocal ? "/rankfactory/cee-booking" : "/cee-booking";

        // Get the base URL dynamically in PHP
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $baseUrl = $protocol . $host . $basePath;

        $successParams = http_build_query([
            'status' => 'success',
            'amt'    => $amount,
            'bid'    => $batch_id,
            'pid'    => $physical_id,
            'op'     => $online_price,
            'pp'     => $physical_price
        ]);

        $transaction_uuid = "CEE-O" . $batch_id . "-P" . $physical_id . "-" . time();

        // eSewa v2 Signature Message Format
        $message = "total_amount=$amount,transaction_uuid=$transaction_uuid,product_code=$product_code";
        $signature = base64_encode(hash_hmac('sha256', $message, $secret_key, true));

        echo json_encode([
            'success' => true,
            'params' => [
                'amount' => $amount,
                'tax_amount' => 0,
                'total_amount' => $amount,
                'transaction_uuid' => $transaction_uuid,
                'product_code' => $product_code,
                'product_service_charge' => 0,
                'product_delivery_charge' => 0,
                'success_url' => $baseUrl . "?" . $successParams . "&",
                'failure_url' => $baseUrl . "?status=failed",
                'signed_field_names' => "total_amount,transaction_uuid,product_code",
                'signature' => $signature
            ]
        ]);
        exit;

    case 'get-user-bookings':
        $onlineRes = callApi(URL_MY_BOOKINS, 'GET');
        $physicalRes = callApi(URL_PHYSICAL_BOOKING_CHECK, 'GET');
        header('Content-Type: application/json');

        echo json_encode([
            'online' => $onlineRes['body'] ?? [],
            'physical' => $physicalRes['body']['data'] ?? []
        ]);
        exit;
}
exit;
