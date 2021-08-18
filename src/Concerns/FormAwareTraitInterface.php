<?php

declare(strict_types=1);

namespace Pollen\Form\Concerns;

use Pollen\Form\FormInterface;

interface FormAwareTraitInterface
{
    /**
     * Gets the related form instance.
     *
     * @return FormInterface
     */
    public function form(): FormInterface;

    /**
     * Set the related form instance.
     *
     * @param FormInterface $form
     *
     * @return FormAwareTraitInterface|static
     */
    public function setForm(FormInterface $form): FormAwareTraitInterface;
}