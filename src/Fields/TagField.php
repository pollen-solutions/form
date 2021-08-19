<?php

declare(strict_types=1);

namespace Pollen\Form\Fields;

use Pollen\Form\FormFieldDriver;

class TagField extends FormFieldDriver implements TagFieldInterface
{
    /**
     * List of supported features.
     * @var array|null
     */
    protected ?array $supports = ['wrapper'];

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $args = array_merge([
            'tag'     => 'div',
            'attrs'   => $this->params('attrs', []),
            'content' => $this->getValue(),
        ], $this->getExtras());

        return (string)$this->form()->partial('tag', $args);
    }
}