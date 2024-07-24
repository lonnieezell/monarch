<?php

namespace Monarch\HTTP;

class Header
{
    public function __construct(
        public readonly string $name,
        public readonly string|array $value
    ) {
    }
}
