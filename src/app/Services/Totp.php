<?php

namespace App\Services;

/**
 * Minimal TOTP (RFC 6238) implementation - compatible with Google Authenticator,
 * Authy, etc. No external package - fewer moving parts to debug.
 */
class Totp
{
    private const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    private const PERIOD = 30;      // seconds per code
    private const DIGITS = 6;
    private const WINDOW = 1;       // allow ±1 period for clock drift

    public static function generateSecret(int $length = 20): string
    {
        $bytes = random_bytes($length);
        return self::base32Encode($bytes);
    }

    /**
     * otpauth:// URI - user types the secret manually into their authenticator app
     * (no QR code dependency - keeps this package-free).
     */
    public static function provisioningUri(string $secret, string $accountLabel, string $issuer): string
    {
        $label = rawurlencode($issuer . ':' . $accountLabel);
        $params = http_build_query([
            'secret' => $secret,
            'issuer' => $issuer,
            'algorithm' => 'SHA1',
            'digits' => self::DIGITS,
            'period' => self::PERIOD,
        ]);

        return "otpauth://totp/{$label}?{$params}";
    }

    public static function currentCode(string $secret, ?int $timestamp = null): string
    {
        $timestamp ??= time();
        $counter = intdiv($timestamp, self::PERIOD);

        return self::hotp($secret, $counter);
    }

    /**
     * Verify a user-submitted code, tolerating small clock drift (±WINDOW periods).
     */
    public static function verify(string $secret, string $code, ?int $timestamp = null): bool
    {
        $timestamp ??= time();
        $code = trim($code);

        for ($errorWindow = -self::WINDOW; $errorWindow <= self::WINDOW; $errorWindow++) {
            $counter = intdiv($timestamp, self::PERIOD) + $errorWindow;
            if (hash_equals(self::hotp($secret, $counter), $code)) {
                return true;
            }
        }

        return false;
    }

    private static function hotp(string $secret, int $counter): string
    {
        $key = self::base32Decode($secret);
        $binaryCounter = pack('N*', 0) . pack('N*', $counter); // 8-byte big-endian counter

        $hash = hash_hmac('sha1', $binaryCounter, $key, true);
        $offset = ord($hash[19]) & 0x0F;

        $truncated = (
            ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF)
        );

        $code = $truncated % (10 ** self::DIGITS);

        return str_pad((string) $code, self::DIGITS, '0', STR_PAD_LEFT);
    }

    private static function base32Encode(string $data): string
    {
        $binaryString = '';
        foreach (str_split($data) as $char) {
            $binaryString .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        $encoded = '';
        foreach (str_split($binaryString, 5) as $chunk) {
            $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            $encoded .= self::BASE32_ALPHABET[bindec($chunk)];
        }

        return $encoded;
    }

    private static function base32Decode(string $data): string
    {
        $data = strtoupper(rtrim($data, '='));
        $binaryString = '';

        foreach (str_split($data) as $char) {
            $pos = strpos(self::BASE32_ALPHABET, $char);
            if ($pos === false) {
                continue;
            }
            $binaryString .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }

        $bytes = '';
        foreach (str_split($binaryString, 8) as $byte) {
            if (strlen($byte) === 8) {
                $bytes .= chr(bindec($byte));
            }
        }

        return $bytes;
    }

    public static function generateRecoveryCodes(int $count = 8): array
    {
        return array_map(
            fn () => strtoupper(bin2hex(random_bytes(4))) . '-' . strtoupper(bin2hex(random_bytes(4))),
            range(1, $count)
        );
    }
}
