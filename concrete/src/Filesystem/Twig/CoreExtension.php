<?php

namespace Concrete\Core\Filesystem\Twig;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Area\Area;
use Concrete\Core\Area\ContainerArea;
use Concrete\Core\Area\GlobalArea;
use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Block\View\BlockViewTemplate;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\File\File;
use Concrete\Core\Filesystem\Twig\Extension\FormProxy;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Html\Image;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Page\Container\ContainerBlockInstance;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\View\View;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\Markup;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CoreExtension extends AbstractExtension implements ApplicationAwareInterface, GlobalsInterface
{
    use ApplicationAwareTrait;

    /** @var UserInfoRepository */
    private $userInfoRepository;

    /** @var Repository */
    private $config;

    /** @var ResolverManagerInterface */
    private $urls;

    /** @var User|null */
    private $user = null;

    /** @var UserInfo|null */
    private $userInfo = null;

    /** @var bool */
    private $userLoaded = false;

    public function __construct(
        UserInfoRepository $userInfoRepository,
        Repository $config,
        ResolverManagerInterface $urls
    ) {
        $this->userInfoRepository = $userInfoRepository;
        $this->config = $config;
        $this->urls = $urls;
    }

    public function getFilters(): array
    {
        return [
            /** Preg_replace filter, {{ "baz" | preg_replace('/z/', 'r') }} outputs `bar` */
            new TwigFilter('preg_replace', function ($subject, ...$args) {
                if ($subject instanceof Markup) {
                    $subject = (string) $subject;
                }

                array_splice($args, 2, 0, $subject);

                return preg_replace(...$args);
            }),

            /** Capture output of a method that would otherwise echo directly */
            new TwigFilter('buffer_method', function ($object, $method, $args = [], &$return = null) {
                ob_start();
                $return = $object->$method(...$args);

                return ob_get_clean();
            }),
        ];
    }

    public function getGlobals(): array
    {
        return [
            'form_html' => new FormProxy($this->app->make(Form::class)),
            'user' => $this->app->make(User::class),
        ];
    }

    public function getFunctions(): array
    {
        return array_merge(
            $this->getBasicFunctions(),
            $this->getFileFunctions(),
            $this->getPageFunctions(),
            $this->getAuthFunctions(),
            $this->getUserFunctions(),
        );
    }

    protected function getBasicFunctions(): array
    {
        return [
            /** Include PHP files */
            new TwigFunction('include', function ($__file, array $scopeItems = [], &$result = null): string {
                ob_start();
                unset($scopeItems['__file']);
                extract($scopeItems);
                $result = include $__file;

                return ob_get_clean();
            }),

            /** Require PHP files */
            new TwigFunction('require', function ($__file, array $scopeItems = [], &$result = null): string {
                ob_start();
                unset($scopeItems['__file']);
                extract($scopeItems);
                $result = require $__file;

                return ob_get_clean();
            }),

            /** Require once PHP files */
            new TwigFunction('require_once', function ($__file, array $scopeItems = [], &$result = null): string {
                ob_start();
                unset($scopeItems['__file']);
                extract($scopeItems);
                $result = require $__file;

                return ob_get_clean();
            }),

            /** Translate strings */
            new TwigFunction('t', 't'),
            new TwigFunction('t2', 't2'),
            new TwigFunction('tc', 'tc'),

            /**
             * Escape HTML, this is rarely useful in twig since twig automatically escapes output for us. But it can be
             * useful in some cases, like when interpolating translations: `{{ t('Hello, <em>%s</em>', h($name)) }}`
             */
            new TwigFunction('h', 'h'),

            new TwigFunction('app', function (?string $class = null, ...$args) {
                if ($class === null) {
                    return $this->app;
                }

                return $this->app->make($class, ...$args);
            }),

            /** Access config */
            new TwigFunction('config', function (?string $item = null) {
                if ($item === null) {
                    return $this->config;
                }

                return $this->config->get($item);
            }),

            /** Create a url */
            new TwigFunction('url', function (...$args): ?\League\URL\URLInterface {
                return $this->urls->resolve($args);
            }),

            /** Get the active locale */
            new TwigFunction('active_locale', function (): string {
                return Localization::activeLocale();
            }),

            /** Get the active language */
            new TwigFunction('active_language', function (): string {
                return Localization::activeLanguage();
            }),

            /**
             * Create a new Area
             * Wrapped in `FluentArea` to allow for chaining
             */
            new TwigFunction('area', function (string $handle, ?Page $c = null): FluentArea {
                return new FluentArea(new Area($handle), $c);
            }),

            /**
             * Create a new GlobalArea
             * Wrapped in `FluentArea` to allow for chaining
             */
            new TwigFunction('global_area', function (string $handle, ?Page $c = null): FluentArea {
                return new FluentArea(new GlobalArea($handle), $c);
            }),

            /**
             * Create a new ContainerArea
             * Wrapped in `FluentArea` to allow for chaining
             */
            new TwigFunction('container_area', function (
                ContainerBlockInstance $container,
                string $handle,
                Page $c
            ): FluentArea {
                return new FluentArea(new ContainerArea($container, $handle), $c);
            }),

            /**
             * Create a new Stack
             * Wrapped in `FluentArea` to allow for chaining
             */
            new TwigFunction('stack', function (string $name): ?FluentArea {
                $stack = Stack::getByName($name);

                return $stack ? new FluentArea(Stack::getByName($name)) : null;
            }),

            /**
             * Create a new BlockViewTemplate. This is useful in some cases when wanting to render original core blocks
             */
            new TwigFunction('block_view_template', function ($b): BlockViewTemplate {
                return new BlockViewTemplate($b);
            }),

            /** Render an element */
            new TwigFunction('element', function (...$args): string {
                ob_start();
                View::element(...$args);

                return ob_get_clean();
            }),

            new TwigFunction('csrf_token', function ($tokenName): string {
                ob_start();
                $this->app->make('token')->output($tokenName);

                return ob_get_clean();
            }, ['is_safe' => ['html']]),

            new TwigFunction('csrf_token_value', function ($tokenName): string {
                return $this->app->make('token')->generate($tokenName);
            }, ['is_safe' => ['html']]),
        ];
    }

    protected function getFileFunctions(): array
    {
        return [
            /** Get a file by ID */
            new TwigFunction('file_by_id', [File::class, 'getByID']),
            new TwigFunction('html_image', function (?\Concrete\Core\Entity\File\File $file = null, array $options = []) {
                return new Image($file, $options);
            }),
        ];
    }

    protected function getPageFunctions(): array
    {
        return [
            /** Get the current page */
            new TwigFunction('current_page', [Page::class, 'getCurrentPage']),
            /** Get the page by ID */
            new TwigFunction('page_by_id', [Page::class, 'getByID']),
        ];
    }

    protected function getAuthFunctions(): array
    {
        return [
            /** Get a list of authentication types */
            new TwigFunction('auth_type_list', [AuthenticationType::class, 'getList']),
            /** Get authentication type by handle */
            new TwigFunction('auth_type_by_handle', [AuthenticationType::class, 'getByHandle']),
            /** Get authentication type by ID */
            new TwigFunction('auth_type_by_id', [AuthenticationType::class, 'getByID']),
        ];
    }

    protected function getUserFunctions(): array
    {
        return [
            new TwigFunction('current_user', function (): ?User {
                return $this->loadUser();
            }),

            new TwigFunction('current_user_info', function (): ?UserInfo {
                return $this->loadUserInfo();
            }),

            new TwigFunction('auth_link', function (
                ?string $loginLabel = null,
                ?string $logoutLabel = null
            ): array {
                $u = $this->app->make(User::class);
                $token = $this->app->make('token');

                $loginLabel = $loginLabel ?? t('Log In');
                $logoutLabel = $logoutLabel ?? t('Log Out');

                if (!$u->isRegistered()) {
                    return [
                        'url' => (string) URL::to('/login'),
                        'label' => $loginLabel,
                        'logged_in' => false,
                    ];
                }

                return [
                    'url' => (string) URL::to('/login', 'do_logout', $token->generate('do_logout')),
                    'label' => $logoutLabel,
                    'logged_in' => true,
                ];
            }),

            new TwigFunction('user_avatar', function ($user) {
                $userInfo = null;

                if ($user instanceof User) {
                    $userInfo = $user->getUserInfoObject();
                } elseif (is_int($user)) {
                    $userInfo = $this->app->make(UserInfoRepository::class)->getByID($user);
                } elseif ($user instanceof UserEntity) {
                    $userInfo = $user->getUserInfoObject();
                }

                if ($userInfo) {
                    return $userInfo->getUserAvatar();
                }

                return null;
            }),
        ];
    }

    private function loadUser(): ?User
    {
        if (!$this->userLoaded) {
            $this->userLoaded = true;
            $user = $this->app->make(User::class);
            if ($user->checkLogin()) {
                $this->user = $user;
                $this->userInfo = $user->getUserInfoObject();
            }
        }

        return $this->user;
    }

    private function loadUserInfo(): ?UserInfo
    {
        $user = $this->loadUser();
        if ($user !== null && $this->userInfo === null) {
            $this->userInfo = $this->userInfoRepository->getByID($user->getUserID());
        }

        return $this->userInfo;
    }
}
