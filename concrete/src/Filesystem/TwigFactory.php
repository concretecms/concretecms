<?php

namespace Concrete\Core\Filesystem;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme as PageTheme;
use Twig\Cache\CacheInterface;
use Twig\Environment;
use Twig\Extension\ExtensionInterface;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;

class TwigFactory implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var CacheInterface
     */
    protected $cache;

    protected $debug;

    /** @var array<string, mixed>  */
    protected $globals = [];

    /** @var ExtensionInterface[] */
    protected $extensions = [];

    /** @var array<string, array<string, string>> */
    protected $namespaces = [];

    public function __construct(CacheInterface $cache, bool $debug)
    {
        $this->cache = $cache;
        $this->debug = $debug;
    }

    public function addNamespace(string $path, string $namespace): void
    {
        if (!isset($this->namespaces[$namespace])) {
            $this->namespaces[$namespace] = [];
        }
        $this->namespaces[$namespace][$path] = $path;
    }

    private function addReservedNamespaces(): void
    {
        // Adds @theme as a twig namespace for the current theme. Not sure if there's
        // a better place to do this. I'd like to encapsulate this functionality in
        // the core exstension but I don't think it has access to the Loader object.
        $c = Page::getCurrentPage();
        $theme = $c ? $c->getCollectionThemeObject() : null;
        if (!$theme) {
            $theme = PageTheme::getSiteTheme();
        }
        if ($theme) {
            $this->addNamespace((string) $theme->getThemeDirectory(), 'theme');
        }
    }

    private function registerNamespaces(FilesystemLoader $loader): void
    {
        foreach ($this->namespaces as $namespace => $paths) {
            foreach ($paths as $path) {
                $loader->addPath($path, $namespace);
            }
        }
    }

    public function create(LoaderInterface $loader, array $options = []): Environment
    {
        $options = $options === [] ? $this->defaultOptions() : array_merge($this->defaultOptions(), $options);
        $twig = new \Twig\Environment($loader, $options);
        $twig->addGlobal('app', $this->app);

        if ($loader instanceof FilesystemLoader) {
            $customNamespaces = $this->namespaces;
            $this->addReservedNamespaces();
            $this->registerNamespaces($loader);
            $this->namespaces = $customNamespaces;
        }

        foreach ($this->globals as $name => $value) {
            $twig->addGlobal($name, $value);
        }

        foreach ($this->extensions as &$extension) {
            if (is_string($extension)) {
                $extension = $this->app->make($extension);
            }
            $twig->addExtension($extension);
        }

        return $twig;
    }

    /**
     * @param mixed $value
     */
    public function addGlobal(string $name, $value): void
    {
        $this->globals[$name] = $value;
    }

    public function addExtension(ExtensionInterface $extension): void
    {
        $this->extensions[] = $extension;
    }

    protected function defaultOptions(): array
    {
        return [
            'debug' => $this->debug,
            'cache' => $this->cache,
            'auto_reload' => true,
            'use_yield' => PHP_VERSION_ID >= 80000,
        ];
    }
}
