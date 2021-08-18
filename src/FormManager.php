<?php

declare(strict_types=1);

namespace Pollen\Form;

use Closure;
use LogicException;
use Pollen\Form\Buttons\SubmitButton;
use Pollen\Form\Fields\HtmlField;
use Pollen\Form\Fields\TagField;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Concerns\ResourcesAwareTrait;
use Pollen\Support\Exception\ManagerRuntimeException;
use Pollen\Support\Proxy\ContainerProxy;
use Pollen\Support\Proxy\EventProxy;
use Psr\Container\ContainerInterface as Container;
use RuntimeException;

class FormManager implements FormManagerInterface
{
    use BootableTrait;
    use ConfigBagAwareTrait;
    use ResourcesAwareTrait;
    use ContainerProxy;
    use EventProxy;

    /**
     * Form manager main instance.
     * @var FormManagerInterface|null
     */
    private static ?FormManagerInterface $instance = null;

    /**
     * List of default add-on driver definitions.
     * @var string[]
     */
    private array $defaultAddonDrivers = [];

    /**
     * List of default button driver definitions.
     * @var string[]
     */
    private array $defaultButtonDrivers = [
        'submit' => SubmitButton::class,
    ];

    /**
     * List of default form field driver definitions.
     * @var string[]
     */
    private array $defaultFormFieldDrivers = [
        'html' => HtmlField::class,
        'tag'  => TagField::class,
    ];

    /**
     * Current form instance.
     * @var FormInterface|null
     */
    protected ?FormInterface $currentForm = null;

    /**
     * List of registered add-on driver definitions.
     * @var AddonDriverInterface[][]|string[][]|array
     */
    protected array $addonDriverDefinitions = [];

    /**
     * List of registered add-ons driver instances.
     * @var AddonDriverInterface[]|array
     */
    protected array $resolvedAddonDrivers = [];

    /**
     * List of registered button driver definitions.
     * @var ButtonDriverInterface[][]|string[][]|array
     */
    protected array $buttonDriverDefinitions = [];

    /**
     * List of registered button driver instances.
     * @var ButtonDriverInterface[]|array
     */
    protected array $resolvedButtonDrivers = [];

    /**
     * List of registered form field driver definitions.
     * @var FormFieldDriverInterface[][]|string[][]|array
     */
    protected array $fieldDriverDefinitions = [];

    /**
     * List of registered form field driver instances.
     * @var FormFieldDriverInterface[]|array
     */
    protected array $resolvedFormFieldDrivers = [];

    /**
     * List of registered form definitions.
     * @var FormInterface[][]|string[][]|array
     */
    protected array $formDefinitions = [];

    /**
     * List of registered form instances.
     * @var FormInterface[]
     */
    protected array $forms = [];

    /**
     * @param array $config
     * @param Container|null $container
     *
     * @return void
     */
    public function __construct(array $config = [], ?Container $container = null)
    {
        $this->setConfig($config);

        if ($container !== null) {
            $this->setContainer($container);
        }

        $this->setResourcesBaseDir(dirname(__DIR__) . '/resources');

        if ($this->config('boot_enabled', true)) {
            $this->boot();
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * Retrieve the form manager main instance.
     *
     * @return static
     */
    public static function getInstance(): FormManagerInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new ManagerRuntimeException(sprintf('Unavailable [%s] instance.', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->forms;
    }

    /**
     * @inheritDoc
     */
    public function boot(): FormManagerInterface
    {
        if (!$this->isBooted()) {
            foreach ($this->defaultAddonDrivers as $alias => $abstract) {
                $this->addonDriverDefinitions[$alias] = $abstract;
            }

            foreach ($this->defaultButtonDrivers as $alias => $abstract) {
                $this->buttonDriverDefinitions[$alias] = $abstract;
            }

            foreach ($this->defaultFormFieldDrivers as $alias => $abstract) {
                $this->fieldDriverDefinitions[$alias] = $abstract;
            }

            $this->setBooted();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function buildForm($definition): FormBuilderInterface
    {
        if (!$form = $this->resolve($definition, FormInterface::class, $this->getContainer(), Form::class)) {
            throw new RuntimeException('Unable to build the Form.');
        }

        if (is_array($definition)) {
            $alias = $definition['alias'] ?? null;
            unset($definition['alias']);
            $params = $definition;
        } else {
            $alias = $form->getAlias();
            $params = [];
        }

        if (empty($alias)) {
            throw new RuntimeException('Form requires an alias to work.');
        }

        if (!empty($this->forms[$alias])) {
            throw new RuntimeException(
                sprintf('Another form with alias [%s] already exists.', $alias)
            );
        }

        $this->forms[$alias] = $form->setAlias($alias);
        $this->forms[$alias]->setFormManager($this);
        $this->forms[$alias]->setParams($params);

        return new FormBuilder($this->forms[$alias]->build());
    }

    /**
     * @inheritDoc
     */
    public function get(string $alias): FormInterface
    {
        return $this->forms[$alias] ?? $this->resolveForm($alias);
    }

    /**
     * @inheritDoc
     */
    public function getAddonDriver(string $alias): AddonDriverInterface
    {
        return $this->resolveAddonDriver($alias);
    }

    /**
     * @inheritDoc
     */
    public function getButtonDriver(string $alias): ButtonDriverInterface
    {
        return $this->resolveButtonDriver($alias);
    }

    /**
     * @inheritDoc
     */
    public function getCurrentForm(): ?FormInterface
    {
        return $this->currentForm;
    }

    /**
     * @inheritDoc
     */
    public function getFormFieldDriver(string $alias): FormFieldDriverInterface
    {
        return $this->resolveFormFieldDriver($alias);
    }

    /**
     * @inheritDoc
     */
    public function getFormIndex(FormInterface $form): int
    {
        $alias = $form->getAlias();

        $index = array_search($alias, array_keys($this->forms), true);

        if ($index !== false) {
            return $index;
        }

        throw new LogicException(sprintf('Unable to retrieve Form index with alias [%s].', $alias));
    }

    /**
     * @inheritDoc
     */
    public function registerAddonDriver(
        string $alias,
        $addonDriverDefinition,
        ?Closure $registerCallback = null
    ): FormManagerInterface {
        if (isset($this->addonDriverDefinitions[$alias])) {
            throw new RuntimeException(
                sprintf('Another Form Addon Driver with alias [%s] already registered.', $alias)
            );
        }

        $this->addonDriverDefinitions[$alias] = $addonDriverDefinition;

        if ($registerCallback !== null) {
            $registerCallback($this);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function registerButtonDriver(
        string $alias,
        $buttonDriverDefinition,
        ?Closure $registerCallback = null
    ): FormManagerInterface {
        if (isset($this->buttonDriverDefinitions[$alias])) {
            throw new RuntimeException(
                sprintf('Another Form Button Driver with alias [%s] already registered.', $alias)
            );
        }

        $this->buttonDriverDefinitions[$alias] = $buttonDriverDefinition;

        if ($registerCallback !== null) {
            $registerCallback($this);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function registerFormFieldDriver(
        string $alias,
        $fieldDriverDefinition,
        ?Closure $registerCallback = null
    ): FormManagerInterface {
        if (isset($this->fieldDriverDefinitions[$alias])) {
            throw new RuntimeException(
                sprintf('Another Form Field Driver with alias [%s] already registered.', $alias)
            );
        }

        $this->fieldDriverDefinitions[$alias] = $fieldDriverDefinition;

        if ($registerCallback !== null) {
            $registerCallback($this);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function registerForm(string $alias, $formDefinition): FormManagerInterface
    {
        if (isset($this->formDefinitions[$alias])) {
            throw new RuntimeException(
                sprintf('Another Form with alias [%s] already registered.', $alias)
            );
        }

        $this->formDefinitions[$alias] = $formDefinition;

        return $this;
    }

    /**
     * Resolve an add-on instance by its alias.
     *
     * @param string $alias
     *
     * @return AddonDriverInterface
     */
    protected function resolveAddonDriver(string $alias): AddonDriverInterface
    {
        if (!empty($this->resolvedAddonDrivers[$alias])) {
            return clone $this->resolvedAddonDrivers[$alias];
        }

        if (!$def = $this->addonDriverDefinitions[$alias] ?? null) {
            throw new RuntimeException(sprintf('Form Addon Driver [%s] unresolvable', $alias));
        }

        if (!$addonDriver = $this->resolve($def, AddonDriverInterface::class, $this->getContainer())) {
            throw new RuntimeException(sprintf('Form [%s] unresolvable', $alias));
        }

        unset($this->addonDriverDefinitions[$alias]);

        return $this->resolvedAddonDrivers[$alias] = $addonDriver;
    }

    /**
     * Resolve a button instance by its alias.
     *
     * @param string $alias
     *
     * @return ButtonDriverInterface
     */
    protected function resolveButtonDriver(string $alias): ButtonDriverInterface
    {
        if (!empty($this->resolvedButtonDrivers[$alias])) {
            return clone $this->resolvedButtonDrivers[$alias];
        }

        if (!$def = $this->buttonDriverDefinitions[$alias] ?? null) {
            throw new RuntimeException(sprintf('Form Button Driver [%s] unresolvable.', $alias));
        }

        if (!$buttonDriver = $this->resolve($def, ButtonDriverInterface::class, $this->getContainer())) {
            throw new RuntimeException(sprintf('Form [%s] unresolvable.', $alias));
        }

        unset($this->buttonDriverDefinitions[$alias]);

        return $this->resolvedButtonDrivers[$alias] = $buttonDriver
            ->setAlias($alias)
            ->build();
    }

    /**
     * Resolve a form field instance by its alias.
     *
     * @param string $alias
     *
     * @return FormFieldDriverInterface
     */
    protected function resolveFormFieldDriver(string $alias): FormFieldDriverInterface
    {
        if (!empty($this->resolvedFormFieldDrivers[$alias])) {
            return clone $this->resolvedFormFieldDrivers[$alias];
        }

        if (!$def = $this->fieldDriverDefinitions[$alias] ?? null) {
            $def = new FormFieldDriver();
        }

        if (!$fieldDriver = $this->resolve($def, FormFieldDriverInterface::class, $this->getContainer())) {
            throw new RuntimeException(sprintf('Form [%s] unresolvable', $alias));
        }

        unset($this->fieldDriverDefinitions[$alias]);

        return $this->resolvedFormFieldDrivers[$alias] = $fieldDriver
            ->setAlias($alias)
            ->build();
    }

    /**
     * Resolve a form instance by its alias.
     *
     * @param string $alias
     *
     * @return FormInterface
     */
    protected function resolveForm(string $alias): FormInterface
    {
        if (!empty($this->forms[$alias])) {
            return $this->forms[$alias];
        }

        if (!$def = $this->formDefinitions[$alias] ?? null) {
            throw new RuntimeException(sprintf('Form [%s] unresolvable', $alias));
        }

        if (!$form = $this->resolve($def, FormInterface::class, $this->getContainer(), Form::class)) {
            throw new RuntimeException(sprintf('Form [%s] unresolvable', $alias));
        }

        $params = is_array($def) ? $def : [];

        unset($this->formDefinitions[$alias]);

        $this->forms[$alias] = $form->setAlias($alias);
        $this->forms[$alias]->setFormManager($this);
        $this->forms[$alias]->setParams($params);

        return $this->forms[$alias]->build();
    }

    /**
     * Resolve an instance from a definition.
     *
     * @param string|array|object $definition
     * @param string $contract
     * @param Container|null $container
     * @param string|null $fallbackClass
     *
     * @return AddonDriverInterface|ButtonDriverInterface|FormFieldDriverInterface|FormInterface|null
     */
    protected function resolve(
        $definition,
        string $contract,
        ?Container $container = null,
        ?string $fallbackClass = null
    ): ?object {
        $object = null;

        if ($definition instanceof $contract) {
            $object = $definition;
        }

        if ($container !== null && is_string($definition) && $container->has($definition)) {
            $object = $container->get($definition);
        }

        if ($object === null && is_string($definition) && class_exists($definition)) {
            $object = new $definition();
        }

        if ($object === null && $fallbackClass !== null) {
            $object = new $fallbackClass($definition);
        }

        if ($object instanceof $contract) {
            return $object;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function setCurrentForm(FormInterface $form): FormManagerInterface
    {
        $this->currentForm = $form;

        $this->currentForm->onSetCurrent();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function unsetCurrentForm(): FormManagerInterface
    {
        if ($this->currentForm instanceof FormManagerInterface) {
            $this->currentForm->onUnsetCurrent();
        }

        $this->currentForm = null;

        return $this;
    }
}