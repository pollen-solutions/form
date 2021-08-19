<?php

declare(strict_types=1);

namespace Pollen\Form;

use Closure;
use Pollen\Field\FieldDriverInterface;
use Pollen\Form\Concerns\FormAwareTrait;
use Pollen\Form\Exception\FieldValidateException;
use Pollen\Support\Arr;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\BuildableTrait;
use Pollen\Support\Concerns\ParamsBagAwareTrait;
use Pollen\Support\Html;
use Pollen\Support\Str;
use Pollen\Support\MessagesBag;

use RuntimeException;

class FormFieldDriver implements FormFieldDriverInterface
{
    use BootableTrait;
    use BuildableTrait;
    use FormAwareTrait;
    use ParamsBagAwareTrait;

    /**
     * List of default supported features.
     * @var string[]
     */
    private array $defaultSupports = ['label', 'request', 'tabindex', 'transport', 'wrapper'];

    /**
     * List of supported features for native field types.
     * @var array
     */
    private array $fieldTypeSupports = [
        'button'              => ['request', 'wrapper'],
        'checkbox'            => ['checking', 'label', 'request', 'wrapper', 'tabindex', 'transport'],
        'checkbox-collection' => ['choices', 'label', 'request', 'tabindexes', 'transport', 'wrapper'],
        'datetime-js'         => ['label', 'request', 'tabindexes', 'transport', 'wrapper'],
        'file'                => ['label', 'request', 'upload', 'tabindex', 'wrapper'],
        'hidden'              => ['request', 'transport'],
        'label'               => ['wrapper'],
        'password'            => ['label', 'request', 'tabindex', 'wrapper'],
        'radio'               => ['label', 'request', 'tabindex', 'transport', 'wrapper'],
        'radio-collection'    => ['choices', 'label', 'request', 'tabindexes', 'transport', 'wrapper'],
        'repeater'            => ['label', 'request',  'tabindexes', 'transport', 'wrapper'],
        'select'              => ['choices', 'label', 'request', 'tabindex', 'transport', 'wrapper'],
        'select-js'           => ['choices', 'label', 'request', 'tabindex', 'transport', 'wrapper'],
        'submit'              => ['request', 'tabindex', 'wrapper'],
        'toggle-switch'       => ['request', 'tabindex', 'transport', 'wrapper'],
    ];

    /**
     * Alias.
     * @var string|null
     */
    protected ?string $alias = null;

    /**
     * Default value.
     * @var string|array|callable|null
     */
    protected $defaultValue;

    /**
     * Delegate field instance.
     * @var FieldDriverInterface|null
     */
    protected ?FieldDriverInterface $delegateField = null;

    /**
     * Flag of existing error related to the field.
     * @var bool
     */
    protected bool $error = false;

    /**
     * Pre-render indicator.
     * @var bool
     */
    protected bool $rendering = false;

    /**
     * Slug.
     * @var string|null
     */
    protected ?string $slug = null;

    /**
     * List of supported features.
     * @var array|null
     */
    protected ?array $supports = null;

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function boot(): FormFieldDriverInterface
    {
        if (!$this->isBooted()) {
            if (!$this->form() instanceof FormInterface) {
                throw new RuntimeException('Form Field Driver requires a valid related Form instance');
            }

            $this->form()->event('field.booting.' . $this->getType(), [&$this]);
            $this->form()->event('field.booting', [&$this]);

            $this->parseParams();

            $this->form()->event('field.booted.' . $this->getType(), [&$this]);
            $this->form()->event('field.booted', [&$this]);

            $this->setBooted();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function build(): FormFieldDriverInterface
    {
        if (!$this->isBuilt()) {
            if ($this->alias === null) {
                throw new RuntimeException('Form Field Driver requires must have a valid alias');
            }

            $this->setBuilt();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addExtra(string $key, $value): FormFieldDriverInterface
    {
        return $this->params(["extras.$key" => $value]);
    }

    /**
     * @inheritDoc
     */
    public function addNotice(string $message, string $level = 'error', array $context = []): FormFieldDriverInterface
    {
        $this->form()->addNotice($message, $level, array_merge($context, ['field' => $this->getSlug()]));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function after(): string
    {
        $after = $this->params('after');

        return $after instanceof Closure ? $after($this) : (string)$after;
    }

    /**
     * @inheritDoc
     */
    public function before(): string
    {
        $before = $this->params('before');

        return $before instanceof Closure ? $before($this) : (string)$before;
    }

    /**
     * @inheritDoc
     */
    public function error(string $message, array $context = []): FormFieldDriverInterface
    {
        return $this->addNotice($message, 'error', $context);
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return [
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
    }

    /**
     * @inheritDoc
     */
    public function getAddonOption(string $alias, ?string $key = null, $default = null)
    {
        return is_null($key) ? $this->params("addons.$alias", []) : $this->params("addons.$alias.$key", $default);
    }

    /**
     * @inheritDoc
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultValue()
    {
        return ($default = $this->defaultValue) instanceof Closure ? $default($this) : $default;
    }

    /**
     * @inheritDoc
     */
    public function getExtras(?string $key = null, $default = null)
    {
        return is_null($key) ? $this->params('extras', []) : $this->params("extras.$key", $default);
    }

    /**
     * @inheritDoc
     */
    public function getGroup(): ?FieldGroupDriverInterface
    {
        return $this->form()->group((string)$this->params('group'));
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (string)$this->params('name');
    }

    /**
     * @inheritDoc
     */
    public function getNotices(?string $type = null): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getPosition(): int
    {
        return (int)$this->params('position', 0);
    }

    /**
     * @inheritDoc
     */
    public function getRequired(?string $key = null, $default = null)
    {
        return $this->params('required' . ($key ? ".$key" : ''), $default);
    }

    /**
     * @inheritDoc
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @inheritDoc
     */
    public function getSupports(): array
    {
        return (array)$this->params('supports', []);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return (string)($this->params('title') ?: $this->getSlug());
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return (string)$this->params('type');
    }

    /**
     * @inheritDoc
     */
    public function getValue(bool $raw = true)
    {
        $value = $this->params('value');

        $this->form()->event('field.get.value', [&$value, $this]);

        if (!$raw) {
            $value = Html::e($value);
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getValueOfChoices(bool $raw = true, ?string $glue = ', ')
    {
        $value = Arr::wrap($this->getValue());

        if ($choices = $this->params('choices', [])) {
            foreach ($value as &$v) {
                if (isset($choices[$v])) {
                    $v = $choices[$v];
                }
            }
            unset($v);
        }

        if (!$raw) {
            $value = Html::e($value);
        }

        if (!is_null($glue)) {
            $value = implode($glue, $value);
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function hasLabel(): bool
    {
        return $this->supports('label') && !empty($this->params('label'));
    }

    /**
     * @inheritDoc
     */
    public function hasNotices(?string $level = null): bool
    {
        return $this->form()->messages()->existsForContext(
            ['field' => $this->getSlug()],
            MessagesBag::toMessageBagLevel($level)
        );
    }

    /**
     * @inheritDoc
     */
    public function hasWrapper(): bool
    {
        return $this->supports('wrapper') && !empty($this->params('wrapper'));
    }

    /**
     * @inheritDoc
     */
    public function isRendering(): bool
    {
        return $this->rendering;
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): void
    {
        $param = $this->params();

        if ($this->defaultValue === null) {
            $this->setDefaultValue($param->get('value'));
        }

        $name = $param->get('name');
        if ($name !== null) {
            $param->set(['name' => Html::e($name ?: $this->getSlug())]);
        }

        if (!$param->get('supports')) {
            if ($this->supports !== null) {
                $param->set('supports', $this->supports);
            } else {
                $param->set('supports', $this->fieldTypeSupports[$this->getType()] ?? $this->defaultSupports);
            }
        }

        $transport = $param->get('transport');
        if ($transport && !in_array('transport', $param->get('supports', []), true)) {
            $param->push('supports', 'transport');
        } elseif ($transport === false) {
            $param->set('supports', array_diff($param->get('supports', []), ['transport']));
        }

        $this->persistValue();

        if ($param->get('wrapper')) {
            $param->push('supports', 'wrapper');
        } elseif (in_array('wrapper', $param->get('supports', []), true)) {
            $param->set('wrapper', true);
        }

        if ($required = $param->get('required', false)) {
            if (is_string($required)) {
                $required = ['message' => $required];
            } elseif (!is_array($required)) {
                $required = [];
            }

            $required = array_merge(
                [
                    'tagged'     => true,
                    'check'      => true,
                    'value_none' => '',
                    'call'       => '',
                    'args'       => [],
                    'message'    => 'Le champ "%s" doit être renseigné.',
                    'html5'      => false,
                ],
                $required
            );

            if ($tagged = $required['tagged']) {
                if (is_string($tagged)) {
                    $tagged = ['content' => $tagged];
                } elseif (!is_array($tagged)) {
                    $tagged = [];
                }

                $required['tagged'] = array_merge(
                    [
                        'tag'     => 'span',
                        'attrs'   => [],
                        'content' => '*',
                    ],
                    $tagged
                );
            }

            $required['call'] = !empty($required['value_none']) && empty($required['call']) ? '!equals' : 'notEmpty';
            $required['args'] = !empty($required['value_none']) && empty($required['args'])
                ? [] + [$required['value_none']]
                : [];

            $param->set('required', $required);
        }

        if ($validations = $param->get('validations')) {
            $param->set('validations', $this->parseValidations($validations));
        }

        foreach ($this->form()->addons() as $alias => $addon) {
            $param->set(
                "addons.$alias",
                array_merge(
                    $addon->defaultFieldOptions(),
                    $param->get("addons.$alias", []) ?: []
                )
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function parseValidations($validations, array $results = []): array
    {
        if (is_array($validations)) {
            if (isset($validations['call'])) {
                $results[] = array_merge(
                    [
                        'alias'   => '',
                        'args'    => [],
                        'call'    => 'default',
                        'message' => 'Le format du champ "%s" est invalide.',
                    ],
                    $validations
                );
            } else {
                foreach ($validations as $validation) {
                    $results += $this->parseValidations($validation, $results);
                }
            }
        } elseif (is_string($validations)) {
            $validations = array_map('trim', explode(',', $validations));

            foreach ($validations as $call) {
                $results += $this->parseValidations(['call' => $call], $results);
            }
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function preRender(): FormFieldDriverInterface
    {
        if (!$this->isRendering()) {
            $param = $this->params();

            if (!$param->has('attrs.id')) {
                $param->set('attrs.id', "FormField-input--{$this->getSlug()}_{$this->form()->getIndex()}");
            }

            if (!$param->get('attrs.id')) {
                $param->pull('attrs.id');
            }

            $default_class = "%s FormField-input FormField-input--{$this->getType()} FormField-input--{$this->getSlug()}";

            if (!$param->has('attrs.class')) {
                $param->set('attrs.class', $default_class);
            } else {
                $param->set('attrs.class', sprintf($param->get('attrs.class'), $default_class));
            }

            if (!$param->get('attrs.class')) {
                $param->pull('attrs.class');
            }

            if (!$param->has('attrs.tabindex')) {
                $param->set('attrs.tabindex', $this->getPosition());
            }

            if ($param->get('attrs.tabindex') === false) {
                $param->pull('attrs.tabindex');
            }

            if ($this->hasNotices('error')) {
                $param->set('attrs.aria-invalid', 'true');
            }

            if ($wrapper = $param->get('wrapper')) {
                $wrapper = (is_array($wrapper)) ? $wrapper : [];
                $param->set('wrapper', array_merge(['tag' => 'div', 'attrs' => []], $wrapper));

                if (!$param->has('wrapper.attrs.id')) {
                    $param->set('wrapper.attrs.id', "FormRow--{$this->getSlug()}_{$this->form()->getIndex()}");
                }
                if (!$param->get('wrapper.attrs.id')) {
                    $param->pull('wrapper.attrs.id');
                }

                $default_class = "FormRow FormRow--{$this->getType()} FormRow--{$this->getSlug()}";
                if (!$param->has('wrapper.attrs.class')) {
                    $param->set('wrapper.attrs.class', $default_class);
                } else {
                    $param->set('wrapper.attrs.class', sprintf($param->get('wrapper.attrs.class'), $default_class));
                }
                if (!$param->get('wrapper.attrs.class')) {
                    $param->pull('wrapper.attrs.class');
                }
            }

            if ($param->get('required.tagged')) {
                if (!$param->has('required.tagged.attrs.id')) {
                    $param->set(
                        'required.tagged.attrs.id',
                        "FormField-required--{$this->getSlug()}_{$this->form()->getIndex()}"
                    );
                }
                if (!$param->get('required.tagged.attrs.id')) {
                    $param->pull('required.tagged.attrs.id');
                }

                $default_class = "%s FormField-required FormField-required--{$this->getType()} FormField-required--{$this->getSlug()}";
                if (!$param->has('required.tagged.attrs.class')) {
                    $param->set('required.tagged.attrs.class', $default_class);
                } else {
                    $param->set(
                        'required.tagged.attrs.class',
                        sprintf($param->get('required.tagged.attrs.class'), $default_class)
                    );
                }
                if (!$param->get('required.tagged.attrs.class')) {
                    $param->pull('required.tagged.attrs.class');
                }
            }

            if ($label = $param->get('label')) {
                if (is_string($label)) {
                    $label = ['content' => Str::humanWords($label)];
                } elseif (is_bool($label)) {
                    $label = [];
                }

                $param->set(
                    'label',
                    array_merge(
                        [
                            'tag'      => 'label',
                            'attrs'    => [],
                            'wrapper'  => false,
                            'position' => 'before',
                            'require'  => true,
                        ],
                        is_array($label) ? $label : []
                    )
                );

                if (!$param->has('label.attrs.id')) {
                    $param->set('label.attrs.id', "FormField-label--{$this->getSlug()}_{$this->form()->getIndex()}");
                }

                if (!$param->get('label.attrs.id')) {
                    $param->pull('label.attrs.id');
                }

                $default_class = "%s FormField-label FormField-label--{$this->getType()} FormField-label--{$this->getSlug()}";
                if (!$param->has('label.attrs.class')) {
                    $param->set('label.attrs.class', $default_class);
                } else {
                    $param->set('label.attrs.class', sprintf($param->get('label.attrs.class'), $default_class));
                }

                if (!$param->get('label.attrs.class')) {
                    $param->pull('label.attrs.class');
                }

                if ($for = $param->get('attrs.id')) {
                    $param->set('label.attrs.for', $for);
                }

                if (!$param->has('label.content')) {
                    $param->set('label.content', Str::humanWords($this->getTitle()));
                }

                if (!$param->get('label.content')) {
                    $param->pull('label.content');
                }

                if (($param->pull('label.require')) && $param->get('required.tagged')) {
                    $content = $param->get('label.content');

                    $param->set('label.content', $content . $this->form()->view('field-required', ['field' => $this]));

                    $param->forget('required.tagged');
                }

                if ($param->get('label.wrapper')) {
                    $param->set(
                        'label.wrapper',
                        [
                            'tag'   => 'div',
                            'attrs' => [
                                'id'    => "FormField-labelWrapper--{$this->getSlug()}_{$this->form()->getIndex()}",
                                'class' => "FormField-labelWrapper FormField-labelWrapper--{$this->getType()}" .
                                    " FormField-labelWrapper--{$this->getSlug()}",
                            ],
                        ]
                    );
                }
            }

            $this->rendering = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $args = array_merge(
            $this->getExtras(),
            [
                'name'  => $this->getName(),
                'attrs' => $this->params('attrs', []),
                'label' => false
            ]
        );

        if ($this->supports('choices')) {
            $args['choices'] = $this->params('choices', []);
        }

        $args['value'] = $this->getValue();

        $this->delegateField = $this->form()->field($this->getType(), $args);

        return (string) $this->delegateField;
    }

    /**
     * @inheritDoc
     */
    public function resetValue(): FormFieldDriverInterface
    {
        $this->params(['value' => $this->defaultValue]);

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function setAlias(string $alias): FormFieldDriverInterface
    {
        if ($this->alias === null) {
            $this->alias = $alias;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDefaultValue($value): FormFieldDriverInterface
    {
        $this->defaultValue = $value;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setPosition(int $position = 0): FormFieldDriverInterface
    {
        $this->params(['position' => $position]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function persistValue(): FormFieldDriverInterface
    {
        $value = $this->form()->persistent($this->getName());

        if ($value !== null) {
            $this->setValue($value);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSlug(string $slug): FormFieldDriverInterface
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setValue($value): FormFieldDriverInterface
    {
        $this->form()->event('field.set.value', [&$value, $this]);

        $this->params(['value' => $value]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function supports(string $support): bool
    {
        return in_array($support, $this->getSupports(), true);
    }

    /**
     * @inheritDoc
     */
    public function validate($value = null): void
    {
        if ($value === null) {
            $value = $this->getValue();
        }

        $check = true;

        $this->form()->event('field.validate.' . $this->getType(), [$value, &$this]);
        $this->form()->event('field.validate', [$value, &$this]);

        if ($this->getRequired('check') && !$check = $this->form()->validation()->call(
                $this->getRequired('call'),
                $value,
                $this->getRequired('args', [])
            )
        ) {
            throw (new FieldValidateException($this, sprintf($this->getRequired('message'), $this->getTitle())))
                ->setRequired();
        }

        if ($check && $validations = $this->params('validations', [])) {
            foreach ($validations as $i => $validation) {
                if (!$this->form()->validation()->call($validation['call'], $value, $validation['args'])) {
                    if (!$alias = $validation['alias'] ?: null) {
                        $alias = (is_string($validation['call'])) ? $validation['call'] : $i;
                    }
                    throw (new FieldValidateException($this, sprintf($validation['message'], $this->getTitle())))
                        ->addFlag($alias);
                }
            }
        }

        $this->form()->event('field.validated.' . $this->getType(), [&$this]);
        $this->form()->event('field.validated', [&$this]);
    }
}