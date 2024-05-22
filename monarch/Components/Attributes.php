<?php

namespace Monarch\Components;

/**
 * The Attributes class is responsible for managing attributes
 * for use on a component and its corresponding tag.
 */
class Attributes
{
    private array $attributes = [];
    private array $only = [];
    private array $except = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * Specifies the attributes that should be included,
     * when rendering the attributes as a string.
     *
     * Example:
     *  $attributes->only('name', 'age');
     */
    public function only(...$attributes): self
    {
        $this->only = $attributes;

        return $this;
    }

    /**
     * Specifies the attributes that should be excluded,
     * when rendering the attributes as a string.
     *
     * Example:
     *  $attributes->except('password');
     */
    public function except(...$attributes): self
    {
        $this->except = $attributes;

        return $this;
    }

    /**
     * Determines if a single attribute exists.
     */
    public function has(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Returns a single attribute's value.
     * If the attribute does not exist, null is returned.
     */
    public function get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Merges the given attributes by key with the existing attributes.
     * If the attribute does not exist, it is created.
     *
     * Example:
     * $attributes->merge(['class' => 'text-red-500']);
     */
    public function merge(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if (!isset($this->attributes[$key])) {
                $this->attributes[$key] = $value;
                continue;
            }

            $this->attributes[$key] =  $value . ' ' . $this->attributes[$key];
        }

        return $this;
    }

    /**
     * Converts the attributes to a string of attributes
     * for use in an HTML tag.
     *
     * TODO: Escape the attribute values
     */
    public function __toString(): string
    {
        $attributes = $this->attributes;

        if (!empty($this->only)) {
            $attributes = array_intersect_key($attributes, array_flip($this->only));
        }

        if (!empty($this->except)) {
            $attributes = array_diff_key($attributes, array_flip($this->except));
        }

        return urldecode(
            str_replace("=", '="', http_build_query($attributes, '', '" ', PHP_QUERY_RFC3986)).'"'
        );
    }
}
