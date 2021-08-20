# Pollen Form Component

[![Latest Stable Version](https://img.shields.io/packagist/v/pollen-solutions/form.svg?style=for-the-badge)](https://packagist.org/packages/pollen-solutions/form)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-green?style=for-the-badge)](LICENSE.md)
[![PHP Supported Versions](https://img.shields.io/badge/PHP->=7.4-8892BF?style=for-the-badge&logo=php)](https://www.php.net/supported-versions.php)

Pollen Solutions **Form** Component provides generator and processor tools for PHP web application forms.

## Installation

```bash
composer require pollen-solutions/form
```

## Basic Usage

### Parameters definition

```php
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Pollen\Form\FormManager;

$forms = new FormManager();

$form = $forms->buildForm(
    [
        /**
         * Alias (Recommended)
         * {@internal Alias is used for generate default HTML tag class, title. 
         * By default an SHA-1 string is generated, it is not human readable.} 
         * @var string
         */
        'alias'  => 'auth',
        /**
         * Form fields
         * @see Configuration/Field] below for more details
         * @var array<string, array> 
         */
        'fields' => [
            'login' => [
                'type' => 'text',
            ],
            'pass'  => [
                'type' => 'password',
            ],
        ],
    ]
)->get();

if ($response = $form->handle()->proceed()) {
    (new SapiEmitter())->emit($response->psr());
    exit;
}

echo $form; 
```

### Form instance definition

```php
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Pollen\Form\Form;
use Pollen\Form\FormManager;

$forms = new FormManager();

$form = $forms->buildForm(new class extends Form {
    protected ?string $alias = 'auth';

    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            'fields' => [
                'login' => [
                    'type' => 'text',
                ],
                'pass'  => [
                    'type' => 'password',
                ],
            ],
        ]);
    }
})->get();

if ($response = $form->handle()->proceed()) {
    (new SapiEmitter())->emit($response->psr());
    exit;
}

echo $form;
```

### Dependency injection service definition

```php
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Pollen\Container\Container;
use Pollen\Form\Form;
use Pollen\Form\FormManager;

$container = new Container();

$forms = new FormManager([], $container);

class AuthForm extends Form
{
    /**
     * Alias identifier.
     * @var string|null
     */
    protected ?string $alias = null;
    
    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            'fields' => [
                'login' => [
                    'type' => 'text',
                ],
                'pass'  => [
                    'type' => 'password',
                ],
            ],
        ]);
    }
}

$container->add(AuthForm::class);

$form = $forms->buildForm(AuthForm::class)->get();

if ($response = $form->handle()->proceed()) {
    (new SapiEmitter())->emit($response->psr());
    exit;
}

echo $form;
```

## Configuration

### Form

```php
[
    /**
     * Action attribute in the form HTML tag.
     * @var string $action
     */
    'action'   => '',
    /**
     * List of enabled add-ons|List of enabled add-ons and their parameters.
     * @var string[]|array<string, array> $addons
     */
    'addons'   => [],
    /**
     * HTML content displayed after the form close tag.
     * @var string $after
     */
    'after'    => '',
    /**
     * List of the HTML attributes for the form tag.
     * @var array $attrs.
     */
    'attrs'    => [],
    /**
     * HTML content displayed before the form open tag.
     * @var string $before
     */
    'before'   => '',
    /**
     * List of enabled buttons|List of enabled buttons and their parameters.
     * @var array $buttons Liste des attributs des boutons actifs.
     */
    'buttons'  => [],
    /**
     * Enctype attribute in the form HTML tag.
     * @var string $enctype
     */
    'enctype'  => '',
    /**
     * List of observed and dispatched events.
     * @var array $events
     */
    'events'   => [],
    /**
     * List of field parameters.
     * @var array<string, array> $fields
     */
    'fields'   => [],
    /**
     * HTTP request method of form submission.
     * @var string $method
     */
    'method'   => 'post',
    /**
     * List of option parameters.
     * @var array $options
     */
    'options'  => [],
    /**
     * List of supported features.
     * @var string[] $supports
     */
    'supports' => ['session'],
    /**
     * Form title.
     * @var string $title
     */
    'title'    => $this->getAlias(),
    /**
     * CSRF token activation (recommended).
     * @var bool
     */
    'token'    => true,
    /**
     * List of parameters of the template view|View instance.
     * @var array|ViewInterface $view
     */
    'view'   => [],
    /**
     * List of HTML form wrapper parameters.
     * @var array $wrapper
     */
    'wrapper'  => []
];
```

### Field

```php
[
    /**
     * List of addon parameters.
     * @var array<string, array>|array $addons
     */
    'addons'      => [],
    /**
     * HTML content displayed after the field.
     * @var string $after
     */
    'after'       => '',
    /**
     * List of the HTML tag attributes.
     * @var array $attrs
     */
    'attrs'       => [],
    /**
     * HTML content displayed before the field.
     * @var string $before
     */
    'before'      => '',
    /**
     * List of choices for field with multiple value.
     * @var array $choices
     */
    'choices'     => [],
    /**
     * List of extra parameters.
     * @var array $extras
     */
    'extras'      => [],
    /**
     * Alias of the related group.
     * @var string|null $group
     */
    'group'       => null,
    /**
     * Field label.
     * {@internal true for default|false to hide|array of custom parameters}.
     * @var bool|string|array $label
     */
    'label'       => true,
    /**
     * Name HTML tag attribute in the handle HTTP request.
     * @var string $name Indice de qualification de la variable de requête.
     */
    'name'        => $this->getSlug(),
    /**
     * Display position.
     * @var int $position
     */
    'position'    => 0,
    /**
     * Required field parameters.
     * ---------------------------------------------------------------------------------------------------------
     * {@internal true for default parameters|false to disabling|array of custom parameters.
     * @var bool|string|array $required {
     *
     * HTML tag.
     * {@internal true for default parameters|false to hide|array of custom parameters based.}
     * @see \Pollen\Field\Drivers\RequiredDriver
     * @var bool|string|array $tagged
     *
     * Enable required validation.
     * @var bool $check
     *
     * Value of none value. empty string by default.
     * @var mixed $value_none
     *
     * Validation function name|validation callable.
     * @var string|callable $call
     *
     * List of arguments passed in the validation handle.
     * @var array $args
     *
     * Notification message if validation failed.
     * @type string $message
     * }
     */
    'required'    => false,
    /**
     * List of supported features.
     * @var array $supports label|wrapper|request|tabindex|transport.
     */
    'supports'    => [],
    /**
     * Title.
     * @var string|null $title
     */
    'title'       => null,
    /**
     * Disable the persistent value in the submission HTTP response.
     * {@internal if null, use the support feature value.}
     * @var bool|null $transport
     */
    'transport'   => null,
    /**
     * Field type.
     * @var string $type
     */
    'type'        => 'html',
    /**
     * List of validation tests.
     * ---------------------------------------------------------------------------------------------------------
     * @var string[]|array[][] $validations {
     *
     * Validation function name|validation callable.
     * @var string|callable $call
     *
     * List of arguments passed in the validation handle.
     * @var  array $args
     *
     * Notification message if validation failed.
     * @type string $message
     * }
     */
    'validations' => [],
    /**
     * Current value.
     * @var mixed $value
     */
    'value'       => null,
    /**
     * Field HTML Wrapper.
     * {@internal true for default parameters|false for hide|array of custom parameters.}
     * @var bool|string|array $wrapper
     */
    'wrapper'     => null
];

```

## Stepwise process decomposition

```php
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Pollen\Form\FormManager;
use Pollen\Form\Exception\FieldMissingException;
use Pollen\Form\Exception\FieldValidateException;
use Pollen\Http\Request;

// Step 1 : Instantiate the Form Manager.
$forms = new FormManager();

// Step 2 : Register a Form.
$forms->registerForm(
    'auth',
    [
        'fields' => [
            'login' => [
                'type'     => 'text',
                'required' => true,
            ],
            'pass'  => [
                'type'        => 'password',
                'required'    => true,
                'validations' => 'password',
            ],
        ],
    ]
);

// Step 3 : Get and Boot the Form. After booting the form becomes immutable.
$form = $forms->get('auth')->boot();

// Step 4 : Form validation.
$request = Request::createFromGlobals();
$form->setHandleRequest($request);
$handle = $form->handle();

if ($form->isSubmitted()) {
    try {
        $field = $form->formField('login');
        $field->validate($handle->datas('login'));
    } catch (FieldMissingException $e) {
        $form->error($e->getMessage());
    } catch (FieldValidateException $e) {
        $form->error('Please enter a username.', ['field' => 'login']);
    }

    try {
        $field = $form->formField('pass');
        $field->validate($handle->datas('pass'));
    } catch (FieldMissingException $e) {
        $form->error($e->getMessage());
    } catch (FieldValidateException $e) {
        if ($e->isRequired()) {
            $form->error('Please enter a password.', ['field' => 'pass']);
        } elseif ($e->hasFlag('password')) {
            $form->error('Password format is invalid.', ['field' => 'pass']);
        }
    }

    if (!$form->handle()->isValidated()) {
        $form->handle()->fail();
    } else {
        $form->handle()->success();
    }
       
    /**
     * Redirect response (optionnal but best pratice).
     * {@internal Send form handle redirect response ensure that the form is always display through a GET HTTP method.
     * All request data is then catched and dispatched by the session processor.}
     */
    $response = $form->handle()->redirectResponse();

    if ($form->isSuccessful()) {
        (new SapiEmitter())->emit($response->psr());
    } else {
        (new SapiEmitter())->emit($response->psr());
    }
    exit;
    /** End of redirect response */
}

// Step 5 : Form render
echo $form;
```
