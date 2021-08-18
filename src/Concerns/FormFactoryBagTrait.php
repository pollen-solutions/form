<?php

declare(strict_types=1);

namespace Pollen\Form\Concerns;

use Pollen\Form\AddonDriverInterface;
use Pollen\Form\ButtonDriverInterface;
use Pollen\Form\Exception\FieldMissingException;
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

trait FormFactoryBagTrait
{
    /**
     * Related add-on manager instance.
     * @var AddonsFactoryInterface|null
     */
    private ?AddonsFactoryInterface $addonsFactory = null;

    /**
     * Related button manager instance.
     * @var ButtonsFactoryInterface|null
     */
    private ?ButtonsFactoryInterface $buttonsFactory = null;

    /**
     * Related event dispatcher instance.
     * @var EventFactoryInterface|null
     */
    private ?EventFactoryInterface $eventsFactory = null;

    /**
     * Related form fields manager instance.
     * @var FormFieldsFactoryInterface|null
     */
    private ?FormFieldsFactoryInterface $formFieldsFactory = null;

    /**
     * Related fields group manager instance.
     * @var FieldGroupsFactoryInterface|null
     */
    private ?FieldGroupsFactoryInterface $groupsFactory = null;

    /**
     * Related request handler instance.
     * @var HandleFactoryInterface|null
     */
    private ?HandleFactoryInterface $handleFactory = null;

    /**
     * Related option manager instance.
     * @var OptionsFactoryInterface|null
     */
    private ?OptionsFactoryInterface $optionsFactory = null;

    /**
     * Related session manager instance.
     * @var SessionFactoryInterface|null
     */
    private ?SessionFactoryInterface $sessionFactory = null;

    /**
     * Related validator instance.
     * @var ValidationFactoryInterface|null
     */
    private ?ValidationFactoryInterface $validationFactory = null;

    /**
     * Gets an add-on driver instance by its alias.
     *
     * @param string $alias
     *
     * @return AddonDriverInterface|null
     */
    public function addon(string $alias): ?AddonDriverInterface
    {
        return $this->addons()->get($alias);
    }

    /**
     * Retrieves the add-on manager instance.
     *
     * @return AddonsFactoryInterface|AddonDriverInterface[]
     */
    public function addons(): AddonsFactoryInterface
    {
        return $this->addonsFactory;
    }

    /**
     * Gets a button driver instance by its alias.
     *
     * @param string $alias
     *
     * @return ButtonDriverInterface|null
     */
    public function button(string $alias): ?ButtonDriverInterface
    {
        return $this->buttons()->get($alias);
    }

    /**
     * Retrieves the button manager instance.
     *
     * @return ButtonsFactoryInterface|ButtonDriverInterface[]
     */
    public function buttons(): ButtonsFactoryInterface
    {
        return $this->buttonsFactory;
    }

    /**
     * Dispatches an event.
     *
     * @param string $alias
     * @param array $args
     *
     * @return void
     */
    public function event(string $alias, array $args = []): void
    {
        $this->events()->trigger($alias, $args);
    }

    /**
     * Retrieves the event dispatcher instance.
     *
     * @return EventFactoryInterface
     */
    public function events(): EventFactoryInterface
    {
        return $this->eventsFactory;
    }

    /**
     * Gets a form field driver instance by its alias.
     *
     * @param string $slug
     *
     * @return FormFieldDriverInterface
     */
    public function formField(string $slug): FormFieldDriverInterface
    {
        if ($field = $this->formFields()->get($slug)) {
            return $field;
        }
        throw new FieldMissingException($slug);
    }

    /**
     * Retrieves the form fields manager instance.
     *
     * @return FormFieldsFactoryInterface|FormFieldDriverInterface[]
     */
    public function formFields(): FormFieldsFactoryInterface
    {
        return $this->formFieldsFactory;
    }

    /**
     * Gets a field group driver instance by its alias.
     *
     * @param string $alias
     *
     * @return FieldGroupDriverInterface
     */
    public function group(string $alias): ?FieldGroupDriverInterface
    {
        return $this->groups()->get($alias);
    }

    /**
     * Retrieves the field groups manager instance.
     *
     * @return FieldGroupsFactoryInterface|FieldGroupDriverInterface[]
     */
    public function groups(): FieldGroupsFactoryInterface
    {
        return $this->groupsFactory;
    }

    /**
     * Gets the request handler instance.
     *
     * @return HandleFactoryInterface
     */
    public function handle(): HandleFactoryInterface
    {
        return $this->handleFactory;
    }

    /**
     * Gets a configuration option value.
     *
     * @param string $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function option(string $key, $default = null)
    {
        return $this->options()->params($key, $default);
    }

    /**
     * Retrieves the option manager instance.
     *
     * @return OptionsFactoryInterface
     */
    public function options(): OptionsFactoryInterface
    {
        return $this->optionsFactory;
    }

    /**
     * Retrieves the session manager instance.
     *
     * @return SessionFactoryInterface
     */
    public function session(): SessionFactoryInterface
    {
        return $this->sessionFactory;
    }

    /**
     * Retrieves the validation manager instance.
     *
     * @return ValidationFactoryInterface
     */
    public function validation(): ValidationFactoryInterface
    {
        return $this->validationFactory;
    }

    /**
     * Sets the addon manager.
     *
     * @param AddonsFactoryInterface $addonsFactory
     *
     * @return FormFactoryBagTraitInterface|static
     */
    public function setAddonsFactory(AddonsFactoryInterface $addonsFactory): FormFactoryBagTraitInterface
    {
        $this->addonsFactory = $addonsFactory;

        return $this;
    }

    /**
     * Sets the button manager.
     *
     * @param ButtonsFactoryInterface $buttonsFactory
     *
     * @return FormFactoryBagTraitInterface|static
     */
    public function setButtonsFactory(ButtonsFactoryInterface $buttonsFactory): FormFactoryBagTraitInterface
    {
        $this->buttonsFactory = $buttonsFactory;

        return $this;
    }

    /**
     * Sets the event dispatcher.
     *
     * @param EventFactoryInterface $eventsFactory
     *
     * @return FormFactoryBagTraitInterface|static
     */
    public function setEventFactory(EventFactoryInterface $eventsFactory): FormFactoryBagTraitInterface
    {
        $this->eventsFactory = $eventsFactory;

        return $this;
    }

    /**
     * Sets the form fields manager.
     *
     * @param FormFieldsFactoryInterface $formFieldsFactory
     *
     * @return FormFactoryBagTraitInterface|static
     */
    public function setFormFieldsFactory(FormFieldsFactoryInterface $formFieldsFactory): FormFactoryBagTraitInterface
    {
        $this->formFieldsFactory = $formFieldsFactory;

        return $this;
    }

    /**
     * Sets group of fields manager.
     *
     * @param FieldGroupsFactoryInterface $groupsFactory
     *
     * @return FormFactoryBagTraitInterface|static
     */
    public function setGroupsFactory(FieldGroupsFactoryInterface $groupsFactory): FormFactoryBagTraitInterface
    {
        $this->groupsFactory = $groupsFactory;

        return $this;
    }

    /**
     * Sets the request handler.
     *
     * @param HandleFactoryInterface $handleFactory
     *
     * @return FormFactoryBagTraitInterface|static
     */
    public function setHandleFactory(HandleFactoryInterface $handleFactory): FormFactoryBagTraitInterface
    {
        $this->handleFactory = $handleFactory;

        return $this;
    }

    /**
     * Sets the option manager.
     *
     * @param OptionsFactoryInterface $optionsFactory
     *
     * @return FormFactoryBagTraitInterface|static
     */
    public function setOptionsFactory(OptionsFactoryInterface $optionsFactory): FormFactoryBagTraitInterface
    {
        $this->optionsFactory = $optionsFactory;

        return $this;
    }

    /**
     * Sets the option manager.
     *
     * @param SessionFactoryInterface $sessionFactory
     *
     * @return FormFactoryBagTraitInterface|static
     */
    public function setSessionFactory(SessionFactoryInterface $sessionFactory): FormFactoryBagTraitInterface
    {
        $this->sessionFactory = $sessionFactory;

        return $this;
    }

    /**
     * Sets the validator.
     *
     * @param ValidationFactoryInterface $validationFactory
     *
     * @return FormFactoryBagTraitInterface|static
     */
    public function setValidationFactory(ValidationFactoryInterface $validationFactory): FormFactoryBagTraitInterface
    {
        $this->validationFactory = $validationFactory;

        return $this;
    }
}