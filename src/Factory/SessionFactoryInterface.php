<?php

declare(strict_types=1);

namespace Pollen\Form\Factory;

use Pollen\Form\Concerns\FormAwareTraitInterface;
use Pollen\Session\AttributeKeyBagInterface;
use Pollen\Session\FlashBagInterface;
use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Proxy\SessionProxyInterface;

interface SessionFactoryInterface extends
    AttributeKeyBagInterface,
    BootableTraitInterface,
    FormAwareTraitInterface,
    SessionProxyInterface
{
    /**
     * Booting.
     *
     * @return SessionFactoryInterface
     */
    public function boot(): SessionFactoryInterface;

    /**
     * Clears the session datas attributes and return all of them.
     *
     * @return array
     */
    public function clear(): array;

    /**
     * Retrieve flash session instance|Set list of flash datas|Get a flash data value.
     *
     * @param string|array|null $key
     * @param mixed $default
     *
     * @return string|array|object|null|FlashBagInterface
     */
    public function flash($key = null, $default = null);

    /**
     * Gets the CSRF protection token payload.
     *
     * @return string
     */
    public function getToken(): string;

    /**
     * Checks the CSRF protection token integrity for a value.
     *
     * @param string $value
     *
     * @return bool
     */
    public function verifyToken(string $value): bool;
}