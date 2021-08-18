<?php

declare(strict_types=1);

namespace Pollen\Form\Concerns;

use Pollen\Form\AddonDriverInterface;
use Pollen\Form\ButtonDriverInterface;
use Pollen\Form\Factory\AddonsFactoryInterface;
use Pollen\Form\Factory\ButtonsFactoryInterface;
use Pollen\Form\Factory\EventFactoryInterface;
use Pollen\Form\Factory\FormFieldsFactoryInterface;
use Pollen\Form\Factory\FieldGroupsFactoryInterface;
use Pollen\Form\Factory\HandleFactoryInterface;
use Pollen\Form\Factory\OptionsFactoryInterface;
use Pollen\Form\Factory\SessionFactoryInterface;
use Pollen\Form\Factory\ValidationFactoryInterface;
use Pollen\Form\FormFieldDriverInterface;
use Pollen\Form\FieldGroupDriverInterface;

interface FormFactoryBagTraitInterface
{
    /**
     * Gets an add-on driver instance by its alias.
     *
     * @param string $alias
     *
     * @return AddonDriverInterface|null
     */
    public function addon(string $alias): ?AddonDriverInterface;

    /**
     * Retrieves the add-on manager instance.
     *
     * @return AddonsFactoryInterface|AddonDriverInterface[]
     */
    public function addons(): AddonsFactoryInterface;

    /**
     * Gets a button driver instance by its alias.
     *
     * @param string $alias
     *
     * @return ButtonDriverInterface|null
     */
    public function button(string $alias): ?ButtonDriverInterface;

    /**
     * Retrieves the button manager instance.
     *
     * @return ButtonsFactoryInterface|ButtonDriverInterface[]
     */
    public function buttons(): ButtonsFactoryInterface;

    /**
     * Dispatches an event.
     *
     * @param string $alias
     * @param array $args
     *
     * @return void
     */
    public function event(string $alias, array $args = []): void;

    /**
     * Retrieves the event dispatcher instance.
     *
     * @return EventFactoryInterface
     */
    public function events(): EventFactoryInterface;

    /**
     * Gets a form field driver instance by its alias.
     *
     * @param string $slug
     *
     * @return FormFieldDriverInterface
     */
    public function formField(string $slug): FormFieldDriverInterface;

    /**
     * Retrieves the form fields manager instance.
     *
     * @return FormFieldsFactoryInterface|FormFieldDriverInterface[]
     */
    public function formFields(): FormFieldsFactoryInterface;

    /**
     * Gets a field group driver instance by its alias.
     *
     * @param string $alias
     *
     * @return FieldGroupDriverInterface
     */
    public function group(string $alias): ?FieldGroupDriverInterface;

    /**
     * Retrieves the field groups manager instance.
     *
     * @return FieldGroupsFactoryInterface|FieldGroupDriverInterface[]
     */
    public function groups(): FieldGroupsFactoryInterface;

    /**
     * Gets the request handler instance.
     *
     * @return HandleFactoryInterface
     */
    public function handle(): HandleFactoryInterface;

    /**
     * Gets a configuration option value.
     *
     * @param string $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function option(string $key, $default = null);

    /**
     * Retrieves the option manager instance.
     *
     * @return OptionsFactoryInterface
     */
    public function options(): OptionsFactoryInterface;

    /**
     * Retrieves the session manager instance.
     *
     * @return SessionFactoryInterface
     */
    public function session(): SessionFactoryInterface;

    /**
     * Retrieves the validation manager instance.
     *
     * @return ValidationFactoryInterface
     */
    public function validation(): ValidationFactoryInterface;

    /**
     * Sets the addon manager.
     *
     * @param AddonsFactoryInterface $addonsFactory
     *
     * @return FormFactoryBagTraitInterface|static
     */
    public function setAddonsFactory(AddonsFactoryInterface $addonsFactory): FormFactoryBagTraitInterface;

    /**
     * Sets the button manager.
     *
     * @param ButtonsFactoryInterface $buttonsFactory
     *
     * @return FormFactoryBagTraitInterface|static
     */
    public function setButtonsFactory(ButtonsFactoryInterface $buttonsFactory): FormFactoryBagTraitInterface;

    /**
     * Sets the event dispatcher.
     *
     * @param EventFactoryInterface $eventsFactory
     *
     * @return FormFactoryBagTraitInterface|static
     */
    public function setEventFactory(EventFactoryInterface $eventsFactory): FormFactoryBagTraitInterface;

    /**
     * Sets the form fields manager.
     *
     * @param FormFieldsFactoryInterface $formFieldsFactory
     *
     * @return FormFactoryBagTraitInterface|static
     */
    public function setFormFieldsFactory(FormFieldsFactoryInterface $formFieldsFactory): FormFactoryBagTraitInterface;

    /**
     * Sets group of fields manager.
     *
     * @param FieldGroupsFactoryInterface $groupsFactory
     *
     * @return FormFactoryBagTraitInterface|static
     */
    public function setGroupsFactory(FieldGroupsFactoryInterface $groupsFactory): FormFactoryBagTraitInterface;

    /**
     * Sets the request handler.
     *
     * @param HandleFactoryInterface $handleFactory
     *
     * @return FormFactoryBagTraitInterface|static
     */
    public function setHandleFactory(HandleFactoryInterface $handleFactory): FormFactoryBagTraitInterface;

    /**
     * Sets the option manager.
     *
     * @param OptionsFactoryInterface $optionsFactory
     *
     * @return FormFactoryBagTraitInterface|static
     */
    public function setOptionsFactory(OptionsFactoryInterface $optionsFactory): FormFactoryBagTraitInterface;

    /**
     * Sets the option manager.
     *
     * @param SessionFactoryInterface $sessionFactory
     *
     * @return FormFactoryBagTraitInterface|static
     */
    public function setSessionFactory(SessionFactoryInterface $sessionFactory): FormFactoryBagTraitInterface;

    /**
     * Sets the validator.
     *
     * @param ValidationFactoryInterface $validationFactory
     *
     * @return FormFactoryBagTraitInterface|static
     */
    public function setValidationFactory(ValidationFactoryInterface $validationFactory): FormFactoryBagTraitInterface;
}