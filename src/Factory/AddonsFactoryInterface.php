<?php

declare(strict_types=1);

namespace Pollen\Form\Factory;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Illuminate\Support\Collection;
use Pollen\Form\AddonDriverInterface;
use Pollen\Form\Concerns\FormAwareTraitInterface;
use Pollen\Support\Concerns\BootableTraitInterface;

interface AddonsFactoryInterface extends
    ArrayAccess,
    BootableTraitInterface,
    Countable,
    FormAwareTraitInterface,
    IteratorAggregate
{
    /**
     * Returns the full list of registered add-on driver instances.
     *
     * @return AddonDriverInterface[]|array
     */
    public function all(): array;

    /**
     * Booting.
     *
     * @return static
     */
    public function boot(): AddonsFactoryInterface;

    /**
     * Iterable collection instance of add-on driver instances.
     *
     * @param array|null $items All registered driver instances if null.
     *
     * @return Collection|AddonDriverInterface[]|iterable
     */
    public function collect(?array $items = null): iterable;

    /**
     * Gets an add-on driver instance by its alias.
     *
     * @param string $alias
     *
     * @return AddonDriverInterface|null
     */
    public function get(string $alias): ?AddonDriverInterface;
}