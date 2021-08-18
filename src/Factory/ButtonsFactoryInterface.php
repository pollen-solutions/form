<?php

declare(strict_types=1);

namespace Pollen\Form\Factory;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Illuminate\Support\Collection;
use Pollen\Form\ButtonDriverInterface;
use Pollen\Form\Concerns\FormAwareTraitInterface;
use Pollen\Support\Concerns\BootableTraitInterface;

interface ButtonsFactoryInterface extends
    ArrayAccess,
    BootableTraitInterface,
    Countable,
    FormAwareTraitInterface,
    IteratorAggregate
{
    /**
     * Returns the full list of registered button driver instances.
     *
     * @return ButtonDriverInterface[]|array
     */
    public function all(): array;

    /**
     * Booting.
     *
     * @return static
     */
    public function boot(): ButtonsFactoryInterface;

    /**
     * Iterable collection instance of button driver instances.
     *
     * @param array|null $items All registered driver instances if null.
     *
     * @return Collection|ButtonDriverInterface[]|iterable
     */
    public function collect(?array $items = null): iterable;

    /**
     * Gets a button driver instance by its alias.
     *
     * @param string $alias
     *
     * @return ButtonDriverInterface|null
     */
    public function get(string $alias): ?ButtonDriverInterface;
}