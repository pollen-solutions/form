<?php

declare(strict_types=1);

namespace Pollen\Form;

use Closure;
use InvalidArgumentException;
use Pollen\Form\Concerns\FormFactoryBagTrait;
use Pollen\Form\Factory\AddonsFactory;
use Pollen\Form\Factory\ButtonsFactory;
use Pollen\Form\Factory\EventFactory;
use Pollen\Form\Factory\FormFieldsFactory;
use Pollen\Form\Factory\FieldGroupsFactory;
use Pollen\Form\Factory\HandleFactory;
use Pollen\Form\Factory\OptionsFactory;
use Pollen\Form\Factory\SessionFactory;
use Pollen\Form\Factory\ValidationFactory;
use Pollen\Http\RequestInterface;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\BuildableTrait;
use Pollen\Support\Concerns\MessagesBagAwareTrait;
use Pollen\Support\Concerns\ParamsBagAwareTrait;
use Pollen\Support\MessagesBag;
use Pollen\Support\Proxy\HttpRequestProxy;
use Pollen\Support\Proxy\FieldProxy;
use Pollen\Support\Proxy\PartialProxy;
use Pollen\Support\Proxy\ViewProxy;
use Pollen\Translation\Concerns\LabelsBagAwareTrait;
use Pollen\View\ViewInterface;
use RuntimeException;

class Form implements FormInterface
{
    use BootableTrait;
    use BuildableTrait;
    use FieldProxy;
    use FormFactoryBagTrait;
    use HttpRequestProxy;
    use LabelsBagAwareTrait;
    use MessagesBagAwareTrait;
    use ParamsBagAwareTrait;
    use PartialProxy;
    use ViewProxy;

    /**
     * Form manager main instance.
     * @var FormManagerInterface|null
     */
    private ?FormManagerInterface $formManager = null;

    /**
     * Render build flags.
     * @var array<string, bool>
     */
    private array $renderBuild = [
        'attrs'   => false,
        'fields'  => false,
        'id'      => false,
        'notices' => false,
    ];

    /**
     * Alias identifier.
     * @var string|null
     */
    protected ?string $alias = null;

    /**
     * Index in form manager.
     * @var int|null
     */
    protected ?int $index = null;

    /**
     * HTTP handle request instance.
     * @var RequestInterface|null
     */
    protected ?RequestInterface $handleRequest = null;

    /**
     * Successful submitted form flag.
     * @var bool
     */
    protected bool $successful = false;

    /**
     * Identifier name of the form in the HTML tag attributes.
     * @var string|null
     */
    protected ?string $tagName = null;

    /**
     * Template view instance.
     * @var ViewInterface|null
     */
    protected ?ViewInterface $view = null;

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
    public function boot(): FormInterface
    {
        if (!$this->isBooted()) {
            $this->event('form.booting', [&$this]);

            $this->parseParams();

            $services = [
                'events',
                'session',
                'addons',
                'formFields',
                'groups',
                'buttons',
                'options',
                'validate',
            ];

            foreach ($services as $service) {
                $service .= 'Factory';

                $this->{$service}->boot();
            }

            $this->setSuccessful((bool)$this->session()->flash('successful', false));

            $this->setBooted();

            $this->event('form.booted', [&$this]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function build(): FormInterface
    {
        if (!$this->isBuilt()) {
            if (!$this->formManager instanceof FormManagerInterface) {
                throw new RuntimeException('Form must running through a related FormManager.');
            }

            if ($this->addonsFactory === null) {
                $this->setAddonsFactory(new AddonsFactory());
            }
            $this->addons()->setForm($this);

            if ($this->buttonsFactory === null) {
                $this->setButtonsFactory(new ButtonsFactory());
            }
            $this->buttons()->setForm($this);

            if ($this->eventsFactory === null) {
                $this->setEventFactory(new EventFactory());
            }
            $this->events()->setForm($this);

            if ($this->formFieldsFactory === null) {
                $this->setFormFieldsFactory(new FormFieldsFactory());
            }
            $this->formFields()->setForm($this);

            if ($this->groupsFactory === null) {
                $this->setGroupsFactory(new FieldGroupsFactory());
            }
            $this->groups()->setForm($this);

            if ($this->handleFactory === null) {
                $this->setHandleFactory(new HandleFactory());
            }
            $this->handle()->setForm($this);

            if ($this->optionsFactory === null) {
                $this->setOptionsFactory(new OptionsFactory());
            }
            $this->options()->setForm($this);

            if ($this->sessionFactory === null) {
                $this->setSessionFactory(
                    new SessionFactory(md5('Form' . $this->getAlias()))
                );
            }
            $this->session()->setForm($this);

            if ($this->validationFactory === null) {
                $this->setValidationFactory(new ValidationFactory());
            }
            $this->validation()->setForm($this);

            $this->setBuilt();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addNotice(string $message, string $level = 'error', array $context = []): FormInterface
    {
        $record = $this->messages($message, $level, $context);

        $flash = $this->session()->flash();
        $flash->push($this->session()->getKey() . ".notices.{$record['level']}", $record);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function csrfKey(): string
    {
        $name = $this->params('token');

        if ($name === false) {
            return '';
        }

        if (!is_string($name) || empty($name)) {
            $name = '_token';
        }

        return $name;
    }

    /**
     * @inheritDoc
     */
    public function csrfField(): string
    {
        if ($name = $this->csrfKey()) {
            $value = $this->session()->getToken();

            return "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>";
        }
        return '';
    }

    /**
     * @inheritDoc
     */
    public function defaultLabels(): array
    {
        return [
            'gender'   => $this->params('labels.gender', false),
            'plural'   => $this->params('labels.plural', $this->getTitle()),
            'singular' => $this->params('labels.singular', $this->getTitle()),
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return [
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
             * CSRF token activation (recommended)
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
    }

    /**
     * @inheritDoc
     */
    public function error(string $message, array $context = []): FormInterface
    {
        return $this->addNotice($message, 'error', $context);
    }

    /**
     * @inheritDoc
     */
    public function formManager(): FormManagerInterface
    {
        return $this->formManager;
    }

    /**
     * @inheritDoc
     */
    public function getAction(): string
    {
        return (string)$this->params('action');
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
    public function getAnchor(): string
    {
        if ($anchor = $this->option('anchor')) {
            if (!is_string($anchor)) {
                if ($this->renderBuildWrapper() && ($exists = $this->params('wrapper.attrs.id'))) {
                    $anchor = $exists;
                } elseif ($this->renderBuildId() && ($exists = $this->params('attrs.id'))) {
                    $anchor = $exists;
                } else {
                    $anchor = '';
                }
            }

            if ($anchor) {
                return ltrim($anchor, '#');
            }
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function getAnchorCleanScripts(): string
    {
        if ($anchor = $this->getAnchor()) {
            $js = 'window.addEventListener("load", (event) => {' .
                'if(window.location.href.split("#")[1] === "' . $anchor . '"){' .
                'window.history.pushState("", document.title, window.location.pathname + window.location.search);' .
                '}});';

            return "<script type=\"text/javascript\">/* <![CDATA[ */$js/* ]]> */</script>";
        }
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getHandleRequest(): RequestInterface
    {
        if ($this->handleRequest === null) {
            $this->handleRequest = $this->httpRequest();
        }

        return $this->handleRequest;
    }

    /**
     * @inheritDoc
     */
    public function getIndex(): int
    {
        if ($this->index === null) {
            $this->index = $this->formManager()->getFormIndex($this);
        }
        return $this->index;
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        $method = strtolower($this->params('method', 'post'));

        return in_array($method, ['get', 'post']) ? $method : 'post';
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
        return (string)$this->params('title');
    }

    /**
     * @inheritDoc
     */
    public function hasError(): bool
    {
        return $this->messages()->exists(MessagesBag::ERROR);
    }

    /**
     * @inheritDoc
     */
    public function hasGroup(): bool
    {
        return $this->groups()->count() > 0;
    }

    /**
     * @inheritDoc
     */
    public function isSubmitted(): bool
    {
        return $this->handle()->isSubmitted();
    }

    /**
     * @inheritDoc
     */
    public function isUploadEnabled(): bool
    {
        return $this->params('enctype') === 'multipart/form-data' || $this->formFields()->hasUploadField();
    }

    /**
     * @inheritDoc
     */
    public function isSuccessful(): bool
    {
        return $this->successful;
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): void
    {
        $this->parseLabels();
    }

    /**
     * @inheritDoc
     */
    public function onSetCurrent(): void
    {
        $this->event('form.set.current', [&$this]);
    }

    /**
     * @inheritDoc
     */
    public function onUnsetCurrent(): void
    {
        $this->event('form.unset.current', [&$this]);
    }

    /**
     * @inheritDoc
     */
    public function persistent(string $key, $default = null)
    {
        return $this->session()->get("request.$key", $default);
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->renderBuild();

        $groups = $this->groups();
        $fields = $this->formFields()->preRender();
        $buttons = $this->buttons();
        $notices = $this->messages()->fetchMessages(
            [
                MessagesBag::ERROR,
                MessagesBag::INFO,
                MessagesBag::SUCCESS,
                MessagesBag::WARNING,
            ]
        );

        return $this->view(
            $this->params('view.template_name') ?? 'index',
            compact('buttons', 'fields', 'groups', 'notices')
        );
    }

    /**
     * @inheritDoc
     */
    public function renderBuild(): FormInterface
    {
        return $this
            ->renderBuildId()
            ->renderBuildWrapper()
            ->renderBuildAttrs()
            ->renderBuildNotices();
    }

    /**
     * @inheritDoc
     */
    public function renderBuildAttrs(): FormInterface
    {
        if ($this->renderBuild['attrs'] === false) {
            $param = $this->params();

            $default_class = "FormContent FormContent--{$this->tagName()}";
            if (!$param->has('attrs.class')) {
                $param->set('attrs.class', $default_class);
            } else {
                $param->set('attrs.class', sprintf($param->get('attrs.class'), $default_class));
            }
            if (!$param->get('attrs.class')) {
                $param->pull('attrs.class');
            }

            $param->set('attrs.action', $this->getAction());

            $param->set('attrs.method', $this->getMethod());
            if ($enctype = $param->get('enctype')) {
                $param->set('attrs.enctype', $enctype);
            } elseif ($this->formFields()->hasUploadField()) {
                $enctype = 'multipart/form-data';
                $param->set('enctype', $enctype);
                $param->set('attrs.enctype', $enctype);
            }

            $this->renderBuild['attrs'] = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function renderBuildId(): FormInterface
    {
        if ($this->renderBuild['id'] === false) {
            $param = $this->params();

            if (!$param->has('attrs.id')) {
                $param->set('attrs.id', "FormContent--{$this->tagName()}");
            }
            if (!$param->get('attrs.id')) {
                $param->pull('attrs.id');
            }

            $this->renderBuild['id'] = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function renderBuildNotices(): FormInterface
    {
        if ($this->renderBuild['notices'] === false) {
            if (!$this->messages()->count() && ($notices = $this->session()->flash('notices'))) {
                foreach ($notices as $type => $items) {
                    foreach ($items as $item) {
                        $this->messages($item['message'] ?? '', $type, $item['context'] ?? []);
                    }
                }
            }

            if ($this->isSuccessful()) {
                if (($mes = $this->option('success', '')) && !$this->messages()->exists(MessagesBag::SUCCESS)) {
                    $this->addNotice($mes, 'success');
                }
                $this->session()->clear();
            }

            $this->renderBuild['successful'] = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function renderBuildWrapper(): FormInterface
    {
        if (($this->renderBuild['wrapper'] ?? false) !== true) {
            $param = $this->params();

            $wrapper = $param->get('wrapper');

            if ($wrapper !== false) {
                $param->set(
                    'wrapper',
                    array_merge(
                        [
                            'tag' => 'div',
                        ],
                        is_array($wrapper) ? $wrapper : []
                    )
                );

                if (!$param->has('wrapper.attrs.id')) {
                    $param->set('wrapper.attrs.id', 'Form--' . $this->tagName());
                }

                if (!$param->has('wrapper.attrs.class')) {
                    $param->set('wrapper.attrs.class', 'Form');
                }
            }

            $this->renderBuild['wrapper'] = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setAlias(string $alias): FormInterface
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setFormManager(FormManagerInterface $formManager): FormInterface
    {
        $this->formManager = $formManager;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setHandleRequest(RequestInterface $handleRequest): FormInterface
    {
        $this->handleRequest = $handleRequest;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSuccessful(bool $status = true): FormInterface
    {
        $this->successful = $status;

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
    public function tagName(): string
    {
        return $this->tagName = is_null($this->tagName)
            ? lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_', '.'], ' ', $this->getAlias()))))
            : $this->tagName;
    }

    /**
     * @inheritDoc
     */
    public function view(?string $name = null, array $data = [])
    {
        if ($this->view === null) {
            $this->view = $this->viewResolver();
        }

        if (func_num_args() === 0) {
            return $this->view;
        }

        return $this->view->render($name, $data);
    }

    /**
     * Resolves view instance.
     *
     * @return ViewInterface
     */
    protected function viewResolver(): ViewInterface
    {
        $default = $this->formManager()->config('view', []);
        $viewDef = $this->params('view');

        if (!$viewDef instanceof ViewInterface) {
            $directory = $this->params('view.directory');
            if ($directory && !file_exists($directory)) {
                $directory = null;
            }

            $overrideDir = $this->params('view.override_dir');
            if ($overrideDir && !file_exists($overrideDir)) {
                $overrideDir = null;
            }

            if ($directory === null && isset($default['directory'])) {
                $default['directory'] = rtrim($default['directory'], '/');
                if (file_exists($default['directory'])) {
                    $directory = $default['directory'];
                }
            }

            if ($overrideDir === null && isset($default['override_dir'])) {
                $default['override_dir'] = rtrim($default['override_dir'], '/');
                if (file_exists($default['override_dir'])) {
                    $overrideDir = $default['override_dir'];
                }
            }

            if ($directory === null) {
                $directory = $this->formManager()->resources('/views');
                if (!file_exists($directory)) {
                    throw new InvalidArgumentException(
                        sprintf('Form [%s] must have an accessible view directory', $this->getAlias())
                    );
                }
            }

            $view = $this->viewManager()->createView('plates')
                ->setDirectory($directory);

            if ($overrideDir !== null) {
                $view->setOverrideDir($overrideDir);
            }
        } else {
            $view = $viewDef;
        }

        $functions = [
            'isSuccessful',
            'tagName',
        ];
        foreach ($functions as $fn) {
            $view->addExtension($fn, [$this, $fn]);
        }

        $view
            ->addExtension('form', $this)
            ->addExtension('csrf', [$this, 'csrfField'])
            ->addExtension('before', function () {
                if ($content = $this->params('before')) {
                    if ($content instanceof Closure) {
                        return $content();
                    }
                    if (is_string($content)) {
                        return $content;
                    }
                }
                return '';
            })
            ->addExtension('after', function () {
                if ($content = $this->params('after')) {
                    if ($content instanceof Closure) {
                        return $content();
                    }
                    if (is_string($content)) {
                        return $content;
                    }
                }
                return '';
            });

        //$this->formManager()->setCurrentForm($this);

        return $view;
    }
}