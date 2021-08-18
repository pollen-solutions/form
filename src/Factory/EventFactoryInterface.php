<?php

declare(strict_types=1);

namespace Pollen\Form\Factory;

use Pollen\Event\TriggeredListenerInterface;
use Pollen\Form\Concerns\FormAwareTraitInterface;
use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Proxy\EventProxyInterface;

interface EventFactoryInterface extends BootableTraitInterface, FormAwareTraitInterface, EventProxyInterface
{
    /**
     * Booting.
     *
     * @return static
     */
    public function boot(): EventFactoryInterface;

    /**
     * Listen an event.
     *
     * @param string $name
     * @param string|callable|TriggeredListenerInterface $listener
     * @param int $priority
     *
     * @return static
     */
    public function on(string $name, $listener, int $priority = 0): EventFactoryInterface;

    /**
     * Dispatch an event.
     *
     * @param string $name
     * @param array $args
     *
     * @return void
     */
    public function trigger(string $name, array $args = []): void;
}