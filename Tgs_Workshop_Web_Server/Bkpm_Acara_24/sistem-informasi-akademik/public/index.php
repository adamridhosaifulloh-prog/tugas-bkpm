<?php
// Header CORS (Acara 24): berlaku untuk seluruh resource yang melewati Router.
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Preflight request dari browser (cross-origin dengan header Authorization / method PUT, DELETE).
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../app/core/autoload.php';

(new Router())->dispatch();
