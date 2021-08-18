<?php

declare(strict_types=1);

namespace Pollen\Form;

interface FormBuilderInterface
{
    /**
     * Gets the related form instance.
     * 
     * @return FormInterface
     */
    public function get(): FormInterface;
}