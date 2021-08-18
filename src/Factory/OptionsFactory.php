<?php

declare(strict_types=1);

namespace Pollen\Form\Factory;

use Pollen\Form\Concerns\FormAwareTrait;
use Pollen\Form\FormInterface;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ParamsBagDelegateTrait;
use RuntimeException;

class OptionsFactory implements OptionsFactoryInterface
{
    use BootableTrait;
    use FormAwareTrait;
    use ParamsBagDelegateTrait;

    /**
     * @inheritDoc
     */
    public function boot(): OptionsFactoryInterface
    {
        if (!$this->isBooted()) {
            if (!$this->form() instanceof FormInterface) {
                throw new RuntimeException('Form OptionsFactory requires a valid related Form instance.');
            }

            $this->form()->event('options.booting');

            $this->params((array)$this->form()->params('options', []));

            $this->parseParams();

            $this->form()->event('options.booted');

            $this->setBooted();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return [
            /**
             * DOM anchor after form submission.
             * {@internal true for auto-enable|false to disabling|HTML tag id string.}
             * @var string|bool $anchor
             */
            'anchor'  => false,
            /**
             * List of error parameters.
             * @var array $error
             */
            'error'   => [
                'title'       => '',
                'show'        => -1,
                'teaser'      => '...',
                'field'       => false,
                'dismissible' => false,
            ],
            /**
             * Success message.
             * @var string|null $success
             */
            'success' => 'The form was submitted with success.',
        ];
    }
}