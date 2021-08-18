<?php

declare(strict_types=1);

namespace Pollen\Form;

use Illuminate\Support\Collection;
use Pollen\Form\Concerns\FormAwareTraitInterface;
use Pollen\Form\Factory\FieldGroupsFactoryInterface;
use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\ParamsBagAwareTraitInterface;

interface FieldGroupDriverInterface extends
    BootableTraitInterface,
    FormAwareTraitInterface,
    ParamsBagAwareTraitInterface
{
    /**
     * Booting.
     *
     * @return static
     */
    public function boot(): FieldGroupDriverInterface;

    /**
     * Gets the content displayed after.
     *
     * @return string
     */
    public function after(): string;

    /**
     * Gets the content displayed before.
     *
     * @return string
     */
    public function before(): string;

    /**
     * Gets the alias identifier.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Gets the list of HTML tag attributes.
     *
     * @param bool $linearized
     *
     * @return string|array
     */
    public function getAttrs(bool $linearized = true);

    /**
     * Gets the list of related field instances.
     *
     * @return Collection|FormFieldDriver[]|array
     */
    public function getFormFields(): iterable;

    /**
     * Gets the group index.
     *
     * @return int
     */
    public function getIndex(): int;

    /**
     * Gets the display position.
     *
     * @return int
     */
    public function getPosition(): int;

    /**
     * Retrieve the related group manager instance.
     *
     * @return FieldGroupsFactoryInterface
     */
    public function groupsManager(): FieldGroupsFactoryInterface;

    /**
     * Sets the alias identifier.
     *
     * @param string $alias
     *
     * @return static
     */
    public function setAlias(string $alias): FieldGroupDriverInterface;

    /**
     * Sets the related group manager instance.
     *
     * @param FieldGroupsFactoryInterface $groupsManager
     *
     * @return static
     */
    public function setGroupManager(FieldGroupsFactoryInterface $groupsManager): FieldGroupDriverInterface;

    /**
     * Set the group index.
     *
     * @param int $index
     *
     * @return static
     */
    public function setIndex(int $index): FieldGroupDriverInterface;
}