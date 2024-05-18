<?php

namespace Monarch\HTTP;

class Cookie
{
    public function __construct(
        public readonly string $name,
        public readonly string $value,
        public readonly int $expires = 0,
        public readonly string $path = '/',
        public readonly string $domain = '',
        public readonly bool $secure = false,
        public readonly bool $httpOnly = true
    ) {
        //
    }
}
