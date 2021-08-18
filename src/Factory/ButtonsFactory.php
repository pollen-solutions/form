<?php

declare(strict_types=1);

namespace Pollen\Form\Factory;

use ArrayIterator;
use Illuminate\Support\Collection;
use Pollen\Form\ButtonDriverInterface;
use Pollen\Form\FormInterface;
use Pollen\Form\Concerns\FormAwareTrait;
use Pollen\Support\Concerns\BootableTrait;
use RuntimeException;

class ButtonsFactory implements ButtonsFactoryInterface
{
    use BootableTrait;
    use FormAwareTrait;

    /**
     * List of the registered button driver instances.
     * @var ButtonDriverInterface[][]|array
     */
    protected array $buttonDrivers = [];

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->buttonDrivers;
    }

    /**
     * @inheritDoc
     */
    public function boot(): ButtonsFactoryInterface
    {
        if (!$this->isBooted()) {
            if (!$this->form() instanceof FormInterface) {
                throw new RuntimeException('Form ButtonsFactory requires a valid related Form instance.');
            }

            $this->form()->event('buttons.booting', [&$this]);

            $buttons = (array)$this->form()->params('buttons', []);

            if (!isset($buttons['submit'])) {
                $buttons['submit'] = true;
            }

            $_buttons = [];
            foreach ($buttons as $alias => $params) {
                if (is_numeric($alias)) {
                    if (is_string($params)) {
                        $alias = $params;
                        $params = [];
                    } else {
                        continue;
                    }
                }

                if ($params !== false) {
                    $_buttons[$alias] = clone $this->form()->formManager()->getButtonDriver($alias);
                    $_buttons[$alias]->setForm($this->form())->setParams(is_array($params) ? $params : []);
                    $_buttons[$alias]->boot();
                }
            }

            $max = $this->collect($_buttons)->max(function (ButtonDriverInterface $button) {
                return $button->getPosition();
            });

            if ($max) {
                $pad = 0;
                $this->collect($_buttons)->each(function (ButtonDriverInterface $button) use (&$pad, $max) {
                    $position = $button->getPosition() ?: ++$pad + $max;

                    return $button->params(['position' => absint($position)]);
                });
            }

            $this->buttonDrivers = $this->collect($_buttons)->sortBy(function (ButtonDriverInterface $button) {
                return $button->getPosition();
            })->all();

            $this->setBooted();

            $this->form()->event('buttons.booted', [&$this]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function collect(?array $items = null): iterable
    {
        return new Collection($items ?? $this->buttonDrivers);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->buttonDrivers);
    }

    /**
     * @inheritDoc
     */
    public function get(string $alias): ?ButtonDriverInterface
    {
        return $this->buttonDrivers[$alias] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->buttonDrivers);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->buttonDrivers);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset): ?ButtonDriverInterface
    {
        return $this->buttonDrivers[$offset] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->buttonDrivers[] = $value;
        } else {
            $this->buttonDrivers[$offset] = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->buttonDrivers[$offset]);
    }
}