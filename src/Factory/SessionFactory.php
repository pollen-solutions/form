<?php

declare(strict_types=1);

namespace Pollen\Form\Factory;

use Pollen\Form\Concerns\FormAwareTrait;
use Pollen\Form\FormInterface;
use Pollen\Session\AttributeKeyBag;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Proxy\SessionProxy;
use RuntimeException;

class SessionFactory extends AttributeKeyBag implements SessionFactoryInterface
{
    use BootableTrait;
    use FormAwareTrait;
    use SessionProxy;

    /**
     * CSRF token identifier.
     * @var string|null
     */
    protected ?string $tokenID = null;

    /**
     * @inheritDoc
     */
    public function boot(): SessionFactoryInterface
    {
        if (!$this->isBooted()) {
            if (!$this->form() instanceof FormInterface) {
                throw new RuntimeException('Form SessionFactory requires a valid related Form instance.');
            }

            $this->form()->event('session.booting', [&$this]);

            $this->session()->addAttributeKeyBag($this->getKey(), $this);

            $this->tokenID = md5('Form|' . $this->form()->getAlias());

            $this->setBooted();

            $this->form()->event('session.booted', [&$this]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function clear(): array
    {
        $this->session()->removeToken($this->tokenID);

        return parent::clear();
    }

    /**
     * @inheritDoc
     */
    public function flash($key = null, $default = null)
    {
        if ($key !== null) {
            $namespace = $this->getKey();

            if (is_string($key)) {
                return $this->session()->flash()->get($namespace . '.' . $key);
            }

            if (is_array($key)) {
                foreach ($key as $k => $v) {
                    unset($key[$k]);
                    $key[$namespace . '.' . $k] = $v;
                }
                $this->session()->flash($key);
            }
        }

        return $this->session()->flash();
    }

    /**
     * @inheritDoc
     */
    public function getToken(): string
    {
        return $this->session()->getToken($this->tokenID);
    }

    /**
     * @inheritDoc
     */
    public function verifyToken(string $value): bool
    {
        return $this->session()->verifyToken($value, $this->tokenID);
    }
}