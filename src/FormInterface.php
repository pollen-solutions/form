<?php

declare(strict_types=1);

namespace Pollen\Form;

use Pollen\Form\Concerns\FormFactoryBagTraitInterface;
use Pollen\Http\RequestInterface;
use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\BuildableTraitInterface;
use Pollen\Support\Concerns\MessagesBagAwareTraitInterface;
use Pollen\Support\Concerns\ParamsBagAwareTraitInterface;
use Pollen\Support\Proxy\FieldProxyInterface;
use Pollen\Support\Proxy\HttpRequestProxyInterface;
use Pollen\Support\Proxy\PartialProxyInterface;
use Pollen\Support\Proxy\ViewProxyInterface;
use Pollen\Translation\Concerns\LabelsBagAwareTraitInterface;
use Pollen\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;

interface FormInterface extends
    BootableTraitInterface,
    BuildableTraitInterface,
    FieldProxyInterface,
    FormFactoryBagTraitInterface,
    HttpRequestProxyInterface,
    LabelsBagAwareTraitInterface,
    MessagesBagAwareTraitInterface,
    ParamsBagAwareTraitInterface,
    PartialProxyInterface,
    ViewProxyInterface
{
    /**
     * Resolves the class as a string and returns the form render.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Booting.
     *
     * @return FormInterface
     */
    public function boot(): FormInterface;

    /**
     * Building.
     *
     * @return FormInterface
     */
    public function build(): FormInterface;

    /**
     * Sets a notification message.
     *
     * @param string $message
     * @param string $level
     * @param array $context
     *
     * @return static
     */
    public function addNotice(string $message, string $level = 'error', array $context = []): FormInterface;

    /**
     * CSRF token key.
     *
     * @return string
     */
    public function csrfKey(): string;

    /**
     * CSRF HTML field render.
     *
     * @return string
     */
    public function csrfField(): string;

    /**
     * List of the form label attributes (plural, singular and gender flag).
     *
     * @return array
     */
    public function defaultLabels(): array;

    /**
     * Sets an error notification message.
     *
     * @param string $message
     * @param array $context
     *
     * @return static
     */
    public function error(string $message, array $context = []): FormInterface;

    /**
     * Retrieve the related form manager instance.
     *
     * @return FormManagerInterface
     */
    public function formManager(): FormManagerInterface;

    /**
     * Gets the action attribute of the form HTML tag.
     *
     * @return string
     */
    public function getAction(): string;

    /**
     * Gets the alias identifier.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Gets the anchor in the DOM.
     *
     * @return string
     */
    public function getAnchor(): string;

    /**
     * Gets the JS script to clean the anchor in browser url.
     *
     * @return string
     */
    public function getAnchorCleanScripts(): string;

    /**
     * Retrieve the handle HTTP request instance.
     *
     * @return RequestInterface|Request
     */
    public function getHandleRequest(): RequestInterface;

    /**
     * Gets the index in the manager.
     *
     * @return int
     */
    public function getIndex(): int;

    /**
     * Gets the form submission HTTP method.
     *
     * @return string
     */
    public function getMethod(): string;

    /**
     * Gets the list of supported features.
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
     * Checks if the form submission throws errors.
     *
     * @return bool
     */
    public function hasError(): bool;

    /**
     * Checks if the form has registered grouped fields.
     *
     * @return bool
     */
    public function hasGroup(): bool;

    /**
     * Checks if the form has been submitted.
     *
     * @return bool
     */
    public function isSubmitted(): bool;

    /**
     * Checks if the form was submitted successfully.
     *
     * @return bool
     */
    public function isSuccessful(): bool;

    /**
     * Checks if the file upload is enabled.
     *
     * @return bool
     */
    public function isUploadEnabled(): bool;

    /**
     * When the form is set as current in the manager.
     *
     * @return void
     */
    public function onSetCurrent(): void;

    /**
     * When the form is unset from current in the manager.
     *
     * @return void
     */
    public function onUnsetCurrent(): void;

    /**
     * Gets a persistent data.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function persistent(string $key, $default = null);

    /**
     * Returns render.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Render building.
     *
     * @return static
     */
    public function renderBuild(): FormInterface;

    /**
     * Render building of HTML tag attributes.
     *
     * @return static
     */
    public function renderBuildAttrs(): FormInterface;

    /**
     * Render building of HTML tag id attribute.
     *
     * @return static
     */
    public function renderBuildId(): FormInterface;

    /**
     * Render building of notification messages.
     *
     * @return static
     */
    public function renderBuildNotices(): FormInterface;

    /**
     * Render building of the form wrapper parameters.
     *
     * @return static
     */
    public function renderBuildWrapper(): FormInterface;

    /**
     * Sets the alias identifier.
     *
     * @param string $alias
     *
     * @return static
     */
    public function setAlias(string $alias): FormInterface;

    /**
     * Sets the form manager instance.
     *
     * @param FormManagerInterface $formManager
     *
     * @return static
     */
    public function setFormManager(FormManagerInterface $formManager): FormInterface;

    /**
     * Sets the handle HTTP request of the form submission.
     *
     * @param RequestInterface $handleRequest
     *
     * @return static
     */
    public function setHandleRequest(RequestInterface $handleRequest): FormInterface;

    /**
     * Set the successful submission flag.
     *
     * @param boolean $status
     *
     * @return static
     */
    public function setSuccessful(bool $status = true): FormInterface;

    /**
     * Checks if a feature is supported.
     *
     * @param string $support
     *
     * @return bool
     */
    public function supports(string $support): bool;

    /**
     * Gets the identifier name of the form in the HTML tag attributes.
     *
     * @return string
     */
    public function tagName(): string;

    /**
     * Resolves view instance|returns a particular template render.
     *
     * @param string|null $name.
     * @param array $data
     *
     * @return ViewInterface|string
     */
    public function view(?string $name = null, array $data = []);
}