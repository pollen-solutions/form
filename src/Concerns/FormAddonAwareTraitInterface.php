<?php

declare(strict_types=1);

namespace Pollen\Form\Concerns;

use Pollen\Form\AddonDriverInterface;
use Pollen\Form\FormInterface;

interface FormAddonAwareTraitInterface
{
    /**
     * Retrieve the related add-on instance.
     *
     * @return AddonDriverInterface
     */
    public function formAddon(): AddonDriverInterface;

    /**
     * Gets the related form instance.
     *
     * @return FormInterface
     */
    public function form(): FormInterface;

    /**
     * Set related form add-on instance.
     *
     * @param AddonDriverInterface $formAddon
     *
     * @return FormAddonAwareTraitInterface|static
     */
    public function setFormAddon(AddonDriverInterface $formAddon): FormAddonAwareTraitInterface;
}