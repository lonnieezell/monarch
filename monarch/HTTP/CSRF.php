<?php

declare(strict_types=1);

namespace Monarch\HTTP;

/**
 * CSRF Protection class
 *
 * @see https://stackoverflow.com/questions/6287903/how-to-properly-add-cross-site-request-forgery-csrf-token-using-php
 */
class CSRF
{
    /**
     * Generates a CSRF token
     *
     * You can lock the token to a the current URI path by passing
     * true as the first argument.
     *
     * Example:
     * // token works for any page on site
     * $token = CSRF::generateToken();
     * // token only works for /login
     * $token = CSRF::generateToken('/login');
     */
    public static function token(bool $lock): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        if (!$lock) {
            return $_SESSION['csrf_token'];
        }

        if (empty($_SESSION['csrf_token_2'])) {
            $_SESSION['csrf_token_2'] = random_bytes(32);
        }

        return hash_hmac('sha256', Request::instance()->path, $_SESSION['csrf_token_2']);
    }

    /**
     * Verifies a CSRF token
     */
    public static function verify(string $token): bool
    {
        if (! isset($_SESSION)) {
            session_start();
        }

        // Handle non-locked tokens
        if (empty($_SESSION['csrf_token_2'])) {
            return hash_equals($_SESSION['csrf_token'] ?? '', $token);
        }

        // Handle locked tokens
        $calc = hash_hmac('sha256', Request::instance()->path, $_SESSION['csrf_token_2']);
        $_SESSION['csrf_token_2'] = null;

        return hash_equals($calc, $token);
    }

    /**
     * Generates a hidden input field with the CSRF token
     */
    public static function input(bool $lock): string
    {
        $token = self::token($lock);

        return "<input type=\"hidden\" name=\"csrf_token\" value=\"{$token}\">";
    }
}
