<?php

declare(strict_types=1);

namespace Pollen\Form\Exception;

use InvalidArgumentException;
use Pollen\Form\FormFieldDriverInterface;
use Throwable;

class FieldValidateException extends InvalidArgumentException implements FormException
{
    /**
     * Identification flags.
     * @var array
     */
    private array $flags = [];

    /**
     * Related field instance.
     * @var FormFieldDriverInterface
     */
    private FormFieldDriverInterface $formField;

    /**
     * @param FormFieldDriverInterface $formField
     * @param string $message
     * @param array $flags
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(FormFieldDriverInterface $formField, string $message = '', array $flags = [], int $code = 0, Throwable $previous = null)
    {
        $this->formField = $formField;

        if (!empty($flags)) {
            $this->addFlags($flags);
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * Adds an identification flag.
     *
     * @param string $flag
     *
     * @return static
     */
    public function addFlag(string $flag): self
    {
        return $this->addFlags([$flag]);
    }

    /**
     * Adds identification flags.
     *
     * @param array $flags
     *
     * @return static
     */
    public function addFlags(array $flags): self
    {
        foreach(array_values($flags) as $flag) {
            if (is_string($flag) && !in_array($flag, $this->flags, true)) {
                $this->flags[] = $flag;
            }
        }

        return $this;
    }

    /**
     * Gets form field instance.
     *
     * @return FormFieldDriverInterface
     */
    public function getFormField(): FormFieldDriverInterface
    {
        return $this->formField;
    }

    /**
     * Checks if flag is registered.
     *
     * @param string $flag
     *
     * @return bool
     */
    public function hasFlag(string $flag): bool
    {
        return in_array($flag, $this->flags, true);
    }

    /**
     * Checks if required.
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->hasFlag('required');
    }

    /**
     * Adds required flag.
     *
     * @return static
     */
    public function setRequired(): self
    {
        return $this->addFlag('required');
    }
}