<?php

declare(strict_types=1);

namespace Monarch\Forms;

use Monarch\HTTP\Request;

/**
 * The Form class is the representation of the content
 * sent via a form through a POST or PUT request.
 *
 * During the initial form rendering, the validation rules
 * would have been set, so the Form class gets those and
 * makes it simple to validate and sanitize the form data.
 */
class Form
{
    private array $validationRules = [];
    private array $formData = [];
    private ?array $errors = null;
    private ?array $forms = null;

    public static function createFromRequest(Request $request): Form
    {
        $form = new static();
        $form->formData = $request->body;   // ?????

        return $form;
    }

    /**
     * Sets the validation rules to use.
     * @param array<string, string> $rules
     */
    public function withValidationRules(array $rules): static
    {
        $this->validationRules = $rules;

        return $this;
    }

    /**
     * Validates the form data based on the
     * validation rules.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $this->errors = [];

        foreach ($this->validationRules as $field => $rules) {
            foreach ($rules as $rule) {
                if (! $rule->validate($this->formData[$field])) {
                    $this->errors[$field] = $rule->message();
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * Returns an array of errors caused during validation.
     */
    public function errors(): ?array
    {
        return $this->errors;
    }

    /**
     * Returns the form data.
     */
    public function data(): array
    {
        return $this->formData;
    }

    /**
     * Checks to see if the form has a value for the specified key.
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->formData);
    }

    /**
     * Returns the value for the specified key if it exists.
     * If the key doesn't exist it will return the default value.
     */
    public function value(string $key, mixed $default): mixed
    {
        return $this->formData[$key] ?? $default;
    }

    /**
     * Returns the form data as an array.
     *
     * @TODO add files to the form data
     */
    public function asArray(): array
    {
        return $this->formData;
    }
}
