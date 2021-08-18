<?php

declare(strict_types=1);

namespace Pollen\Form\Concerns;

use Pollen\Form\FormInterface;

trait FormAwareTrait
{
    /**
     * Related form instance.
     * @var FormInterface|null
     */
    private ?FormInterface $form = null;

    /**
     * Gets the related form instance.
     *
     * @return FormInterface
     */
    public function form(): FormInterface
    {
        return $this->form;
    }

    /**
     * Set the related form instance.
     *
     * @param FormInterface $form
     *
     * @return FormAwareTraitInterface|static
     */
    public function setForm(FormInterface $form): FormAwareTraitInterface
    {
        $this->form = $form;

        return $this;
    }
}