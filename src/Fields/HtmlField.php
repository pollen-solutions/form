<?php

declare(strict_types=1);

namespace Pollen\Form\Fields;

use Closure;
use Pollen\Form\FormFieldDriver;

class HtmlField extends FormFieldDriver implements HtmlFieldInterface
{
    /**
     * Liste des propriétés de formulaire supportées.
     * @var array
     */
    protected $supports = [];

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $value = $this->getValue();

        return $value instanceof Closure ? $value() : (string)$value;
    }
}