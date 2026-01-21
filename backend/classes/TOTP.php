<?php

/**
 * TOTP (Time-based One-Time Password) Handler
 * 
 * Implements RFC 6238 for time-based one-time passwords
 * Used for Google Authenticator, Authy, Microsoft Authenticator, etc.
 * 
 * No external dependencies - pure PHP implementation
 */
class TOTP
{
    private const DIGITS = 6;
    private const PERIOD = 30; // seconds
    private const ALGORITHM = 'sha1';

    /**
     * Generate a random secret key
     * @param int $length Length of the secret (default 32 for good security)
     * @return string Base32 encoded secret
     */
    public static function generateSecret($length = 32)
    {
        $bytes = random_bytes($length);
        return self::base32Encode($bytes);
    }

    /**
     * Generate QR Code URL for authenticator apps
     * @param string $secret Base32 encoded secret
     * @param string $email User's email
     * @param string $issuer Company/app name
     * @return string URL to QR code image
     */
    public static function getQRCodeURL($secret, $email, $issuer = 'jquerynativePHPapi')
    {
        $params = [
            'secret' => $secret,
            'issuer' => $issuer,
            'accountname' => $email,
        ];

        $label = urlencode($issuer . ':' . $email);
        $query = http_build_query($params);
        $otpauthUrl = 'otpauth://totp/' . $label . '?' . $query;

        // Using QR Server API for QR code generation (more reliable than Google Charts)
        return 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($otpauthUrl);
    }

    /**
     * Verify a TOTP code
     * @param string $secret Base32 encoded secret
     * @param string $code 6-digit code from authenticator app
     * @param int $timeTolerance Allow code from ±30 seconds (1 = ±0 seconds)
     * @return bool True if code is valid
     */
    public static function verify($secret, $code, $timeTolerance = 1)
    {
        $code = trim($code);

        // Validate code format
        if (!is_numeric($code) || strlen($code) !== self::DIGITS) {
            return false;
        }

        $secretBytes = self::base32Decode($secret);
        $currentTime = time();

        // Check current time and surrounding windows (for clock skew tolerance)
        for ($i = -$timeTolerance; $i <= $timeTolerance; $i++) {
            $timeCounter = intdiv($currentTime, self::PERIOD) + $i;
            $expectedCode = self::generateCode($secretBytes, $timeCounter);

            if ($expectedCode === $code) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate TOTP code for a specific time
     * @param string $secretBytes Binary secret
     * @param int $timeCounter Time counter (seconds / 30)
     * @return string 6-digit TOTP code
     */
    private static function generateCode($secretBytes, $timeCounter)
    {
        // Convert time counter to binary
        $timeCounterBinary = pack('N', 0) . pack('N', $timeCounter);

        // HMAC-SHA1
        $hmac = hash_hmac(self::ALGORITHM, $timeCounterBinary, $secretBytes, true);

        // Dynamic truncation
        $offset = ord($hmac[19]) & 0x0f;
        $value = unpack('N', substr($hmac, $offset, 4))[1];
        $value = ($value & 0x7fffffff) % pow(10, self::DIGITS);

        // Pad with zeros
        return str_pad($value, self::DIGITS, '0', STR_PAD_LEFT);
    }

    /**
     * Base32 encode (RFC 4648)
     * @param string $data Binary data
     * @return string Base32 encoded string
     */
    private static function base32Encode($data)
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $output = '';

        for ($i = 0; $i < strlen($data); $i += 5) {
            $chunk = substr($data, $i, 5);

            if (strlen($chunk) < 5) {
                $chunk = str_pad($chunk, 5, "\0");
            }

            $bytes = array_values(unpack('C*', $chunk));

            $output .= $alphabet[($bytes[0] & 248) >> 3];
            $output .= $alphabet[(($bytes[0] & 7) << 2) | (($bytes[1] & 192) >> 6)];
            $output .= isset($bytes[1]) ? $alphabet[($bytes[1] & 62) >> 1] : '=';
            $output .= isset($bytes[1]) ? $alphabet[(($bytes[1] & 1) << 4) | (($bytes[2] & 240) >> 4)] : '=';
            $output .= isset($bytes[2]) ? $alphabet[(($bytes[2] & 15) << 1) | (($bytes[3] & 128) >> 7)] : '=';
            $output .= isset($bytes[3]) ? $alphabet[($bytes[3] & 124) >> 2] : '=';
            $output .= isset($bytes[3]) ? $alphabet[(($bytes[3] & 3) << 3) | (($bytes[4] & 224) >> 5)] : '=';
            $output .= isset($bytes[4]) ? $alphabet[$bytes[4] & 31] : '=';
        }

        return $output;
    }

    /**
     * Base32 decode (RFC 4648)
     * @param string $data Base32 encoded string
     * @return string Binary data
     */
    private static function base32Decode($data)
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $data = strtoupper($data);
        $output = '';

        for ($i = 0; $i < strlen($data); $i += 8) {
            $chunk = substr($data, $i, 8);
            $chunk = str_pad($chunk, 8, '=');

            $bytes = [];
            for ($j = 0; $j < 8; $j++) {
                if ($chunk[$j] === '=') {
                    $bytes[$j] = 0;
                } else {
                    $bytes[$j] = strpos($alphabet, $chunk[$j]);
                }
            }

            $output .= chr(($bytes[0] << 3) | ($bytes[1] >> 2));

            if ($chunk[2] !== '=') {
                $output .= chr((($bytes[1] & 3) << 6) | ($bytes[2] << 1) | ($bytes[3] >> 4));
            }

            if ($chunk[4] !== '=') {
                $output .= chr((($bytes[3] & 15) << 4) | ($bytes[4] >> 1));
            }

            if ($chunk[5] !== '=') {
                $output .= chr((($bytes[4] & 1) << 7) | ($bytes[5] << 2) | ($bytes[6] >> 3));
            }

            if ($chunk[7] !== '=') {
                $output .= chr((($bytes[6] & 7) << 5) | $bytes[7]);
            }
        }

        return $output;
    }
}
