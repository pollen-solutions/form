<?php

declare(strict_types=1);

namespace Pollen\Form;

use Closure;
use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\ConfigBagAwareTraitInterface;
use Pollen\Support\Concerns\ResourcesAwareTraitInterface;
use Pollen\Support\Proxy\ContainerProxyInterface;
use Pollen\Support\Proxy\EventProxyInterface;

interface FormManagerInterface extends
    BootableTraitInterface,
    ConfigBagAwareTraitInterface,
    ResourcesAwareTraitInterface,
    ContainerProxyInterface,
    EventProxyInterface
{
    /**
     * Returns the full list of registered form instance.
     *
     * @return FormInterface[]|array
     */
    public function all(): array;

    /**
     * Booting.
     *
     * @return static
     */
    public function boot(): FormManagerInterface;

    /**
     * Build a form by its definition.
     *
     * @param string|array|FormInterface $definition
     *
     * @return FormBuilderInterface
     */
    public function buildForm($definition): FormBuilderInterface;

    /**
     * Gets a registered form instance by its alias.
     *
     * @param string $alias
     *
     * @return FormInterface
     */
    public function get(string $alias): FormInterface;

    /**
     * Gets a registered add-on driver instance by its alias.
     *
     * @param string $alias
     *
     * @return AddonDriverInterface
     */
    public function getAddonDriver(string $alias): AddonDriverInterface;

    /**
     * Gets a registered button driver instance by its alias.
     *
     * @param string $alias
     *
     * @return ButtonDriverInterface
     */
    public function getButtonDriver(string $alias): ButtonDriverInterface;

    /**
     * Get the current form instance.
     *
     * @return FormInterface|null
     */
    public function getCurrentForm(): ?FormInterface;

    /**
     * Gets a registered form field driver instance by its alias.
     *
     * @param string $alias
     *
     * @return FormFieldDriverInterface
     */
    public function getFormFieldDriver(string $alias): FormFieldDriverInterface;

    /**
     * Get a form index by its instance.
     *
     * @param FormInterface $form
     *
     * @return int
     */
    public function getFormIndex(FormInterface $form): int;

    /**
     * Sets an add-on driver.
     *
     * @param string $alias
     * @param string|array|AddonDriverInterface $addonDriverDefinition
     * @param Closure|null $registerCallback
     *
     * @return static
     */
    public function registerAddonDriver(
        string $alias,
        $addonDriverDefinition,
        ?Closure $registerCallback = null
    ): FormManagerInterface;

    /**
     * Sets a button driver.
     *
     * @param string $alias
     * @param string|array|ButtonDriverInterface $buttonDriverDefinition
     * @param Closure|null $registerCallback
     *
     * @return static
     */
    public function registerButtonDriver(
        string $alias,
        $buttonDriverDefinition,
        ?Closure $registerCallback = null
    ): FormManagerInterface;

    /**
     * Sets a form field driver.
     *
     * @param string $alias
     * @param string|array|FormFieldDriverInterface $fieldDriverDefinition
     * @param Closure|null $registerCallback
     *
     * @return static
     */
    public function registerFormFieldDriver(
        string $alias,
        $fieldDriverDefinition,
        ?Closure $registerCallback = null
    ): FormManagerInterface;

    /**
     * Register a form.
     *
     * @param string $alias
     * @param string|array|FormInterface $formDefinition
     *
     * @return static
     */
    public function registerForm(string $alias, $formDefinition): FormManagerInterface;

    /**
     * Sets the current form.
     *
     * @param FormInterface $form
     *
     * @return static
     */
    public function setCurrentForm(FormInterface $form): FormManagerInterface;

    /**
     * Unsets the current form.
     *
     * @return static
     */
    public function unsetCurrentForm(): FormManagerInterface;
}