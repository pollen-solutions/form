<?php

declare(strict_types=1);

namespace Pollen\Form\Concerns;

use LogicException;
use Pollen\Form\AddonDriverInterface;
use Pollen\Form\FormInterface;

trait FormAddonAwareTrait
{
    /**
     * Related form add-on instance.
     * @var AddonDriverInterface|null
     */
    private ?AddonDriverInterface $formAddon = null;

    /**
     * Related form instance.
     * @var FormInterface|null
     */
    private ?FormInterface $form = null;

    /**
     * Retrieve the related add-on instance.
     *
     * @return AddonDriverInterface
     */
    public function formAddon(): AddonDriverInterface
    {
        if ($this->formAddon instanceof AddonDriverInterface) {
            return $this->formAddon;
        }

        throw new LogicException('Unavailable related Form addon');
    }

    /**
     * Gets the related form instance.
     *
     * @return FormInterface
     */
    public function form(): FormInterface
    {
        return $this->formAddon()->form();
    }

    /**
     * Set related form add-on instance.
     *
     * @param AddonDriverInterface $formAddon
     *
     * @return FormAddonAwareTraitInterface|static
     */
    public function setFormAddon(AddonDriverInterface $formAddon): FormAddonAwareTraitInterface
    {
        $this->formAddon = $formAddon;

        return $this;
    }
}