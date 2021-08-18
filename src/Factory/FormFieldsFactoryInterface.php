<?php

declare(strict_types=1);

namespace Pollen\Form\Factory;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Illuminate\Support\Collection;
use Pollen\Form\Concerns\FormAwareTraitInterface;
use Pollen\Form\FormFieldDriverInterface;
use Pollen\Support\Concerns\BootableTraitInterface;

interface FormFieldsFactoryInterface extends
    ArrayAccess,
    BootableTraitInterface,
    Countable,
    FormAwareTraitInterface,
    IteratorAggregate
{
    /**
     * Returns the full list of registered form field driver instances.
     *
     * @return FormFieldDriverInterface[]|array
     */
    public function all(): array;

    /**
     * Booting.
     *
     * @return static
     */
    public function boot(): FormFieldsFactoryInterface;

    /**
     * Iterable collection instance of form field driver instances.
     *
     * @param array|null $items All registered instances if null.
     *
     * @return Collection|FormFieldDriverInterface[]|iterable
     */
    public function collect(?array $items = null): iterable;

    /**
     * Gets a form field driver instance by its alias.
     *
     * @param string $alias
     *
     * @return FormFieldDriverInterface|null
     */
    public function get(string $alias): ?FormFieldDriverInterface;

    /**
     * Check if form has an upload field.
     *
     * @return bool
     */
    public function hasUploadField(): bool;

    /**
     * Returns the list of registered form field driver instances for a group.
     *
     * @param string $groupAlias
     *
     * @return Collection|FormFieldDriverInterface[]|null
     */
    public function forGroup(string $groupAlias): ?iterable;

    /**
     * Execute the pre-render for the list of registered form field driver instances.
     *
     * @return FormFieldsFactoryInterface
     */
    public function preRender(): FormFieldsFactoryInterface;
}
