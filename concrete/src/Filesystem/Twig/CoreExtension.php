<?php

namespace Concrete\Core\Filesystem\Twig;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Area\Area;
use Concrete\Core\Area\GlobalArea;
use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Block\View\BlockViewTemplate;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\File\File;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\View\View;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CoreExtension extends AbstractExtension implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var Repository
     */
    private $config;
    /**
     * @var ResolverManagerInterface
     */
    private $urls;

    public function __construct(Repository $config, ResolverManagerInterface $urls)
    {
        $this->config = $config;
        $this->urls = $urls;
    }

    public function getFilters()
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

            /** Capture output of method that would otherwise output to  */
            new TwigFilter('bufferMethod', function ($object, $method, $args, &$return = null) {
                ob_start();
                $return = $object->$method(...$args);
                return ob_get_clean();
            }),
        ];
    }

    public function getFunctions()
    {
        $functions = array_merge(
            $this->getBasicFunctions(),
            $this->getFileFunctions(),
            $this->getPageFunctions(),
            $this->getAuthFunctions(),
        );

        return $functions;
    }

    private function getBasicFunctions(): array
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

            /**
             * Escape HTML, this is rarely useful in twig since twig automatically escapes output for us. But it can be
             * useful in some cases, like when interpolating translations: `{{ t('Hello, <em>%s</em>', h($name)) }}`
             */
            new TwigFunction('h', 'h'),
            new TwigFunction('app', function (?string $class = null, ...$args) {
                if ($class === null) {
                    return $this->app;
                }

                return $this->app->make($class, $args);
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
            new TwigFunction('activeLocale', function (): string {
                return Localization::activeLocale();
            }),

            /** Get the active language */
            new TwigFunction('activeLanguage', function (): string {
                return Localization::activeLanguage();
            }),

            /**
             * Create a new Area
             * Wrapped in `FluentArea` to allow for chaining
             */
            new TwigFunction('Area', function (string $handle, ?Page $c = null): FluentArea {
                return new FluentArea(new Area($handle), $c);
            }),

            /**
             * Create a new GlobalArea
             * Wrapped in `FluentArea` to allow for chaining
             */
            new TwigFunction('GlobalArea', function (string $handle, ?Page $c = null): FluentArea {
                return new FluentArea(new GlobalArea($handle), $c);
            }),

            /**
             * Create a new Stack
             * Wrapped in `FluentArea` to allow for chaining
             */
            new TwigFunction('Stack', function (string $name): FluentArea {
                return new FluentArea(Stack::getByName($name));
            }),

            /**
             * Create a new BlockViewTemplate. This is useful in some cases when wanting to render original core blocks
             */
            new TwigFunction('BlockViewTemplate', function ($b): BlockViewTemplate {
                return new BlockViewTemplate($b);
            }),

            /** Render an element */
            new TwigFunction('element', function (...$args): string {
                ob_start();
                View::element(...$args);
                return ob_get_clean();
            }),
        ];
    }

    private function getFileFunctions(): array
    {
        return [
            /** Get a file by ID */
            new TwigFunction('fileGetByID', [File::class, 'getByID']),
        ];
    }

    private function getPageFunctions(): array
    {
        return [
            /** Get the current page */
            new TwigFunction('getCurrentPage', [Page::class, 'getCurrentPage']),
            /** Get the current page by ID */
            new TwigFunction('pageByID', [Page::class, 'getByID']),
        ];
    }

    private function getAuthFunctions(): array
    {
        return [
            /** Get a list of authentication types */
            new TwigFunction('authTypeGetList', [AuthenticationType::class, 'getList']),
            /** Get authentication type by handle */
            new TwigFunction('authTypeByHandle', [AuthenticationType::class, 'getByHandle']),
            /** Get authentication type by ID */
            new TwigFunction('authTypeByID', [AuthenticationType::class, 'getByID']),
        ];
    }
}