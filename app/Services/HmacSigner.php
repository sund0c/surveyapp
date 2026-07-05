<?php

namespace App\Services;

class HmacSigner
{
    /**
     * Build the canonical string that gets signed.
     * Both server and client MUST construct this identically.
     */
    public static function canonicalString(string $timestamp, string $nonce, string $rawBody): string
    {
        return $timestamp . "\n" . $nonce . "\n" . $rawBody;
    }

    public static function sign(string $timestamp, string $nonce, string $rawBody, string $secret): string
    {
        return hash_hmac('sha256', self::canonicalString($timestamp, $nonce, $rawBody), $secret);
    }

    /**
     * Timing-safe comparison. Always use hash_equals, never `===`, for signature checks.
     */
    public static function verify(string $timestamp, string $nonce, string $rawBody, string $secret, string $providedSignature): bool
    {
        $expected = self::sign($timestamp, $nonce, $rawBody, $secret);

        return hash_equals($expected, $providedSignature);
    }
}
