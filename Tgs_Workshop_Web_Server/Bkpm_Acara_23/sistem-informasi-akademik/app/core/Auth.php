<?php
class Auth
{
    // Untuk praktik lokal. Pada aplikasi nyata, simpan secret di konfigurasi aman.
    const SECRET = 'kunci_rahasia_workshop_2026';

    private static function base64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64urlDecode(string $data)
    {
        $remainder = strlen($data) % 4;
        if ($remainder > 0) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($data, '-_', '+/'), true);
    }

    public static function generateToken(array $payload): string
    {
        $header = self::base64url(json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT',
        ]));

        $payload['exp'] = time() + 3600;
        $payloadEnc = self::base64url(json_encode($payload));
        $signature = self::base64url(hash_hmac(
            'sha256',
            "$header.$payloadEnc",
            self::SECRET,
            true
        ));

        return "$header.$payloadEnc.$signature";
    }

    public static function validateToken(string $token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        [$header, $payload, $signature] = $parts;
        $expectedSignature = self::base64url(hash_hmac(
            'sha256',
            "$header.$payload",
            self::SECRET,
            true
        ));

        if (!hash_equals($expectedSignature, $signature)) {
            return false;
        }

        $decodedPayload = self::base64urlDecode($payload);
        if ($decodedPayload === false) {
            return false;
        }

        $data = json_decode($decodedPayload, true);
        if (!is_array($data) || !isset($data['exp']) || $data['exp'] < time()) {
            return false;
        }

        return $data;
    }

    public static function requireAuth(): array
    {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $authorization = '';

        foreach ($headers as $name => $value) {
            if (strtolower($name) === 'authorization') {
                $authorization = $value;
                break;
            }
        }

        if ($authorization === '' && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authorization = $_SERVER['HTTP_AUTHORIZATION'];
        }

        if (!preg_match('/^Bearer\s+(\S+)$/i', trim($authorization), $matches)) {
            Response::json(401, false, 'Token tidak ditemukan');
        }

        $payload = self::validateToken($matches[1]);
        if (!$payload) {
            Response::json(401, false, 'Token tidak valid atau kedaluwarsa');
        }

        return $payload;
    }
}
