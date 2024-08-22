<?php

declare(strict_types=1);

namespace Tests\Support\Validation;

use Somnambulist\Components\Validation\Rule;

class ValidRule extends Rule
{
    protected string $message = ":attribute is invalid";

    public function check($value): bool
    {
        return $value === 'valid';
    }
}
