<?php

declare(strict_types=1);

namespace Pollen\Form\Fields;

use Closure;
use Pollen\Form\FormFieldDriver;

class HtmlField extends FormFieldDriver implements HtmlFieldInterface
{
    /**
     * List of supported features.
     * @var array|null
     */
    protected ?array $supports = [];

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $value = $this->getValue();

        return $value instanceof Closure ? $value() : (string)$value;
    }
}