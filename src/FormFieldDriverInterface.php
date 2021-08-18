<?php

declare(strict_types=1);

namespace Pollen\Form;

use Pollen\Form\Concerns\FormAwareTraitInterface;
use Pollen\Form\Exception\FieldValidateException;
use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\BuildableTraitInterface;
use Pollen\Support\Concerns\ParamsBagAwareTraitInterface;

interface FormFieldDriverInterface extends
    BootableTraitInterface,
    BuildableTraitInterface,
    FormAwareTraitInterface,
    ParamsBagAwareTraitInterface
{
    /**
     * Resolves the class as a string and returns the field render.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Add an extra parameters.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return static
     */
    public function addExtra(string $key, $value): FormFieldDriverInterface;

    /**
     * Sets a notification message related to the field.
     *
     * @param string $message
     * @param string $level
     * @param array $context
     *
     * @return static
     */
    public function addNotice(string $message, string $level = 'error', array $context = []): FormFieldDriverInterface;

    /**
     * Gets the HTML content displayed after.
     *
     * @return string
     */
    public function after(): string;

    /**
     * Gets the HTML content displayed before.
     *
     * @return string
     */
    public function before(): string;

    /**
     * Booting.
     *
     * @return static
     */
    public function boot(): FormFieldDriverInterface;

    /**
     * Building.
     *
     * @return static
     */
    public function build(): FormFieldDriverInterface;

    /**
     * Sets an error notification message.
     *
     * @param string $message
     * @param array $context
     *
     * @return static
     */
    public function error(string $message, array $context = []): FormFieldDriverInterface;

    /**
     * Gets the alias.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Returns one or full list of an addon parameters.
     * {@internal Full list if $key is null.}
     *
     * @param string $alias
     * @param null|string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getAddonOption(string $alias, ?string $key = null, $default = null);

    /**
     * Gets the default value.
     *
     * @return int|string|array
     */
    public function getDefaultValue();

    /**
     * Returns one or full list of extra parameters.
     * {@internal Full list if $key is null.}
     *
     * @param string|null $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getExtras(?string $key = null, $default = null);

    /**
     * Gets the related group instance.
     *
     * @return FieldGroupDriverInterface
     */
    public function getGroup(): ?FieldGroupDriverInterface;

    /**
     * Get the name of the field in the handle HTTP request of form submission.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns the list of notification messages by type.
     * {@internal Full list if $type is null}.
     *
     * @param string|null $type error|success|notice|warning
     *
     * @return string[]|array<string, array>|array
     */
    public function getNotices(?string $type = null): array;

    /**
     * Get the display position.
     *
     * @return int
     */
    public function getPosition(): int;

    /**
     * Returns one of full list of required field parameters.
     * {@internal Full list if $key is null.}
     *
     * @param string|null $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getRequired(?string $key = null, $default = null);

    /**
     * Gets the slug.
     *
     * @return string
     */
    public function getSlug(): string;

    /**
     * Returns the list of supported features.
     *
     * @return string[]
     */
    public function getSupports(): array;

    /**
     * Get the title.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Get the file type.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Get the current value.
     *
     * @param bool $raw Escape HTML special chars if false.
     *
     * @return mixed
     */
    public function getValue(bool $raw = true);

    /**
     * Returns the value of a choice field.
     *
     * @param bool $raw Escape HTML special chars if false.
     * @param string|null $glue Separator of values in linearized output format. Disable if null and outputs an array.
     *
     * @return string|array
     */
    public function getValueOfChoices(bool $raw = true, ?string $glue = ', ');

    /**
     * Checks if a label exists.
     *
     * @return boolean
     */
    public function hasLabel(): bool;

    /**
     * Check if the field is related with notification message by level.
     * {@internal All levels if $level is null.}
     *
     * @param string|null $level error|success|notice|warning
     *
     * @return bool
     */
    public function hasNotices(?string $level = null): bool;

    /**
     * Check if wrapper exists.
     *
     * @return boolean
     */
    public function hasWrapper(): bool;

    /**
     * Check if pre-render is active.
     *
     * @return bool
     */
    public function isRendering(): bool;

    /**
     * Parse the validation tests.
     *
     * @param string|array $validations
     * @param array $results
     *
     * @return array
     */
    public function parseValidations($validations, array $results = []): array;

    /**
     * Persistence of the value from the session.
     *
     * @return static
     */
    public function persistValue(): FormFieldDriverInterface;

    /**
     * Build the pre-render.
     *
     * @return static
     */
    public function preRender(): FormFieldDriverInterface;

    /**
     * Returns the render.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Reset value to its default definition.
     *
     * @return static
     */
    public function resetValue(): FormFieldDriverInterface;

    /**
     * Sets the alias.
     *
     * @param string $alias
     *
     * @return static
     */
    public function setAlias(string $alias): FormFieldDriverInterface;

    /**
     * Sets the default value.
     *
     * @param int|string|array|callable $value
     *
     * @return static
     */
    public function setDefaultValue($value): FormFieldDriverInterface;

    /**
     * Sets the display position.
     *
     * @param int $position
     *
     * @return $this
     */
    public function setPosition(int $position = 0): FormFieldDriverInterface;

    /**
     * Sets the slug.
     *
     * @param string $slug
     *
     * @return static
     */
    public function setSlug(string $slug): FormFieldDriverInterface;

    /**
     * Set the value.
     *
     * @param mixed $value
     *
     * @return static
     */
    public function setValue($value): FormFieldDriverInterface;

    /**
     * Check if feature is supported.
     *
     * @param string $support
     *
     * @return bool
     */
    public function supports(string $support): bool;

    /**
     * Validate field value.
     *
     * @param mixed $value
     *
     * @return void
     *
     * @throws FieldValidateException
     */
    public function validate($value = null): void;
}