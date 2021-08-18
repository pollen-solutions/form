<?php

declare(strict_types=1);

namespace Pollen\Form\Factory;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Illuminate\Support\Collection;
use Pollen\Form\Concerns\FormAwareTraitInterface;
use Pollen\Form\FieldGroupDriverInterface;
use Pollen\Support\Concerns\BootableTraitInterface;

interface FieldGroupsFactoryInterface extends
    ArrayAccess,
    BootableTraitInterface,
    Countable,
    FormAwareTraitInterface,
    IteratorAggregate
{
    /**
     * Returns the full list of registered field group driver instances.
     *
     * @return FieldGroupDriverInterface[]|array
     */
    public function all(): array;

    /**
     * Booting.
     *
     * @return static
     */
    public function boot(): FieldGroupsFactoryInterface;

    /**
     * Iterable collection instance of field group driver instances.
     *
     * @param array|null $items All registered instances if null.
     *
     * @return Collection|FieldGroupsFactory[]|iterable
     */
    public function collect(?array $items = null): iterable;

    /**
     * Gets a field group driver instance by its alias.
     *
     * @param string $alias
     *
     * @return FieldGroupDriverInterface|null
     */
    public function get(string $alias): ?FieldGroupDriverInterface;

    /**
     * Retrieves the value of current incrementation.
     *
     * @return int
     */
    public function getIncrement(): int;

    /**
     * Sets a field group driver.
     *
     * @param string $alias
     * @param array|FieldGroupDriverInterface $driverDefinition
     *
     * @return static
     */
    public function setDriver(string $alias, $driverDefinition = []): FieldGroupsFactoryInterface;
}