<?php

declare(strict_types=1);

namespace Pollen\Form\Factory;

use InvalidArgumentException;
use Pollen\Form\Concerns\FormAwareTraitInterface;
use Pollen\Http\RedirectResponse;
use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\ParamsBag;

interface HandleFactoryInterface extends BootableTraitInterface, FormAwareTraitInterface
{
    /**
     * Booting.
     *
     * @return static
     */
    public function boot(): HandleFactoryInterface;

    /**
     * Retrieve the instance of datas|Set a list of additional datas|Get a data value of handle request.
     *
     * @param array|string|null $key
     * @param mixed $default
     *
     * @return string|int|array|mixed|ParamsBag
     *
     * @throws InvalidArgumentException
     */
    public function datas($key = null, $default = null);

    /**
     * Handle of the failed form submission.
     *
     * @return static
     */
    public function fail(): HandleFactoryInterface;

    /**
     * Gets the failed form submission redirect url.
     *
     * @return string
     */
    public function getFailedRedirectUrl(): string;

    /**
     * Gets the successful form submission redirect url.
     *
     * @return string
     */
    public function getSucceedRedirectUrl(): string;

    /**
     * Check if form is submitted.
     *
     * @return boolean
     */
    public function isSubmitted(): bool;

    /**
     * Checks if the form validation is correct.
     *
     * @return bool
     */
    public function isValidated(): bool;

    /**
     * Persist a data in session.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return static
     */
    public function persist(string $key, $value): HandleFactoryInterface;

    /**
     * Handles the full process of form submission.
     *
     * @return RedirectResponse
     */
    public function proceed(): RedirectResponse;

    /**
     * Retrieve the redirect response after the form submission.
     *
     * @return RedirectResponse
     */
    public function redirectResponse(): RedirectResponse;

    /**
     * Sets an error message in safe mode.
     * {@internal If the field is missing, error is set in the form.}
     *
     * @param string $message
     * @param array $context
     * @param string|null $fieldSlug
     *
     * @return static
     */
    public function safeError(
        string $message = '',
        array $context = [],
        string $fieldSlug = null
    ): HandleFactoryInterface;

    /**
     * Sets the redirect url if the form submission is failed.
     *
     * @param string $url
     * @param bool $raw Auto-formatting is enabled if false.
     *
     * @return static
     */
    public function setFailedRedirectUrl(string $url, bool $raw = false): HandleFactoryInterface;

    /**
     * Sets the redirect url if the form submission is successful.
     *
     * @param string $url
     * @param bool $raw Auto-formatting is enabled if false.
     *
     * @return static
     */
    public function setSucceedRedirectUrl(string $url, bool $raw = false): HandleFactoryInterface;

    /**
     * Handle of the successful form submission.
     *
     * @return static
     */
    public function success(): HandleFactoryInterface;

    /**
     * Handle the validation of the form (all fields).
     *
     * @return static
     */
    public function validate(): HandleFactoryInterface;
}