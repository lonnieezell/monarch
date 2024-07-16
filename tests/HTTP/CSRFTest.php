<?php

use Monarch\HTTP\CSRF;
use Monarch\HTTP\Request;

describe('CSRF', function () {
    beforeEach(function () {
        $_SESSION = [];
    });

    it('should generate a CSRF token', function () {
        $token = CSRF::token(false);

        expect($token)->toMatch('/^[a-f0-9]{64}$/i');
        expect($_SESSION['csrf_token'])->toBe($token);
    });

    it('should generate a locked CSRF token', function () {
        $token = CSRF::token(true);

        expect($token)->toMatch('/^[a-f0-9]{64}$/i');
        expect($_SESSION['csrf_token_2'])->toBeString();
    });

    it('should verify a valid CSRF token', function () {
        $token = CSRF::token(false);
        $result = CSRF::verify($token);

        expect($result)->toBe(true);
    });

    it('should verify a valid locked CSRF token', function () {
        $token = CSRF::token(true);
        $result = CSRF::verify($token);

        expect($result)->toBe(true);
        expect($_SESSION['csrf_token_2'])->toBe(null);
    });

    it('should not verify an invalid CSRF token', function () {
        $result = CSRF::verify('invalid_token');

        expect($result)->toBe(false);
    });

    it('should generate a hidden input field with the CSRF token', function () {
        $input = CSRF::input(false);

        expect($input)->toContain('<input type="hidden" name="csrf_token" value="');
        expect($input)->toContain($_SESSION['csrf_token']);
    });
});
