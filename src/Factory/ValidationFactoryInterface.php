<?php

declare(strict_types=1);

namespace Pollen\Form\Factory;

use Pollen\Form\Concerns\FormAwareTraitInterface;
use Pollen\Support\Concerns\BootableTraitInterface;

interface ValidationFactoryInterface extends BootableTraitInterface, FormAwareTraitInterface
{
    /**
     * Booting.
     *
     * @return static
     */
    public function boot(): ValidationFactoryInterface;

    /**
     * Call of an integrity test.
     *
     * @param string|callable $callback
     * @param mixed $value
     * @param array $args
     *
     * @return bool
     */
    public function call($callback, $value, array $args = []): bool;

    /**
     * Call the default integrity test.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function default($value): bool;

    /**
     * Call the comparaison integrity test.
     * @internal ex. for a password and its confirmation value.
     *
     * @param mixed $value
     * @param mixed $tags
     *
     * @return bool
     */
    public function compare($value, $tags): bool;
}