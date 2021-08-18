<?php

declare(strict_types=1);

namespace Pollen\Form;

use Pollen\Form\Concerns\FormAwareTraitInterface;
use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\BuildableTraitInterface;
use Pollen\Support\Concerns\ParamsBagAwareTraitInterface;

interface AddonDriverInterface extends
    BootableTraitInterface,
    BuildableTraitInterface,
    FormAwareTraitInterface,
    ParamsBagAwareTraitInterface
{
    /**
     * Booting.
     *
     * @return static
     */
    public function boot(): AddonDriverInterface;

    /**
     * Building.
     *
     * @return static
     */
    public function build(): AddonDriverInterface;

    /**
     * Returns the list of default add-on parameters of related form.
     *
     * @return array
     */
    public function defaultFormOptions(): array;

    /**
     * Returns the list of default add-on parameters of each fields.
     *
     * @return array
     */
    public function defaultFieldOptions(): array;

    /**
     * Gets the alias identifier.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Sets the alias identifier.
     *
     * @param string $alias
     *
     * @return static
     */
    public function setAlias(string $alias): AddonDriverInterface;
}