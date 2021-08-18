<?php

declare(strict_types=1);

namespace Pollen\Form;

use Pollen\Form\Concerns\FormAwareTraitInterface;
use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\BuildableTraitInterface;
use Pollen\Support\Concerns\ParamsBagAwareTraitInterface;

interface ButtonDriverInterface extends
    BootableTraitInterface,
    BuildableTraitInterface,
    FormAwareTraitInterface,
    ParamsBagAwareTraitInterface
{
    /**
     * Resolves the class as a string and returns the button render.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Booting.
     *
     * @return static
     */
    public function boot(): ButtonDriverInterface;

    /**
     * Building.
     *
     * @return static
     */
    public function build(): ButtonDriverInterface;

    /**
     * Gets the alias identifier.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Gets the display position.
     *
     * @return int
     */
    public function getPosition(): int;

    /**
     * Checks if it has an HTML wrapper.
     *
     * @return bool
     */
    public function hasWrapper(): bool;

    /**
     * Returns the render.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Sets the alias identifier.
     *
     * @param string $alias
     *
     * @return static
     */
    public function setAlias(string $alias): ButtonDriverInterface;
}