<?php

namespace Concrete\Core\Asset;

use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Http\Request;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Routing\RouterInterface;
use Concrete\Core\Support\Facade\Application;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

abstract class Asset implements AssetInterface
{
    /**
     * The handle of this asset (together with getAssetType, identifies this asset).
     *
     * @var string
     */
    protected $assetHandle;

    /**
     * The position of this asset (\Concrete\Core\Asset\AssetInterface::ASSET_POSITION_HEADER or \Concrete\Core\Asset\AssetInterface::ASSET_POSITION_FOOTER).
     *
     * @var string
     */
    protected $position;

    /**
     * Is this asset a locally available file (accessible with the getAssetPath method)?
     *
     * @var bool
     */
    protected $local = true;

    /**
     * Does this asset support minification?
     *
     * @var bool
     */
    protected $assetSupportsMinification = false;

    /**
     * Can this asset be combined with other assets?
     *
     * @var bool
     */
    protected $assetSupportsCombination = false;

    /**
     * The location of the asset (used to build the path & URL).
     *
     * @var string
     */
    protected $location;

    /**
     * The URL of this asset.
     *
     * @var string
     */
    protected $assetURL;

    /**
     * The path to this asset.
     *
     * @var string
     */
    protected $assetPath;

    /**
     * Does the URL/path have already been resolved (starting from the location) for this (local) assets?
     *
     * @var bool
     */
    protected $assetHasBeenMapped = false;

    /**
     * The name of the file of this asset.
     *
     * @var string
     */
    protected $filename;

    /**
     * The asset version.
     *
     * @var string
     */
    protected $assetVersion = '0';

    /**
     * The package that defines this asset.
     *
     * @var \Concrete\Core\Package\Package|\Concrete\Core\Entity\Package|null
     */
    protected $pkg;

    /**
     * The URL of the source files this asset has been built from (useful to understand the origin of this asset).
     *
     * @var array
     */
    protected $combinedAssetSourceFiles = [];

    /**
     * Initialize the instance.
     *
     * @param string $assetHandle the handle of this asset (together with getAssetType, identifies this asset)
     */
    public function __construct($assetHandle = '')
    {
        $this->assetHandle = (string) $assetHandle;
        $this->position = $this->getAssetDefaultPosition();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::setAssetPosition()
     */
    public function setAssetPosition($position)
    {
        $this->position = $position;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::getAssetPosition()
     */
    public function getAssetPosition()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::getAssetHandle()
     */
    public function getAssetHandle()
    {
        return $this->assetHandle;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::getOutputAssetType()
     */
    public function getOutputAssetType()
    {
        return $this->getAssetType();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::setAssetIsLocal()
     */
    public function setAssetIsLocal($isLocal)
    {
        $this->local = $isLocal;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::isAssetLocal()
     */
    public function isAssetLocal()
    {
        return $this->local;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::setAssetSupportsMinification()
     */
    public function setAssetSupportsMinification($minify)
    {
        $this->assetSupportsMinification = $minify;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::assetSupportsMinification()
     */
    public function assetSupportsMinification()
    {
        return $this->local && $this->assetSupportsMinification;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::setAssetSupportsCombination()
     */
    public function setAssetSupportsCombination($combine)
    {
        $this->assetSupportsCombination = $combine;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::assetSupportsCombination()
     */
    public function assetSupportsCombination()
    {
        return $this->local && $this->assetSupportsCombination;
    }

    /**
     * Set the location of this asset.
     *
     * @param string $location
     */
    public function setAssetLocation($location)
    {
        $this->location = $location;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::setAssetURL()
     */
    public function setAssetURL($url)
    {
        $this->assetHasBeenMapped = true;
        $this->assetURL = $url;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::getAssetURL()
     */
    public function getAssetURL()
    {
        if (!$this->assetHasBeenMapped) {
            $this->mapAssetLocation($this->location);
        }

        return $this->assetURL;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::setAssetPath()
     */
    public function setAssetPath($path)
    {
        $this->assetHasBeenMapped = true;
        $this->assetPath = $path;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::getAssetPath()
     */
    public function getAssetPath()
    {
        if (!$this->assetHasBeenMapped) {
            $this->mapAssetLocation($this->location);
        }

        return $this->assetPath;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::mapAssetLocation()
     */
    public function mapAssetLocation($path)
    {
        if ($this->isAssetLocal()) {
            $app = Application::getFacadeApplication();
            $locator = $app->make(FileLocator::class);
            if ($this->pkg) {
                $locator->addLocation(new FileLocator\PackageLocation($this->pkg->getPackageHandle()));
            }
            $r = $locator->getRecord($path);
            $this->setAssetPath($r->file);
            $this->setAssetURL($r->url);
        } else {
            $this->setAssetURL($path);
        }
    }

    /**
     * Does the URL/path have already been resolved (starting from the location) for this (local) assets?
     *
     * @return bool
     */
    public function hasAssetBeenMapped()
    {
        return $this->assetHasBeenMapped;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::getAssetURLPath()
     */
    public function getAssetURLPath()
    {
        return substr($this->getAssetURL(), 0, strrpos($this->getAssetURL(), '/'));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::getAssetFilename()
     */
    public function getAssetFilename()
    {
        return $this->filename;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::getAssetHashKey()
     */
    public function getAssetHashKey()
    {
        $result = $this->assetURL;
        if ($this->isAssetLocal()) {
            $filename = $this->getAssetPath();
            if (is_file($filename)) {
                $useFileContents = false;
                if (is_readable($filename)) {
                    $app = Application::getFacadeApplication();
                    $config = $app->make('config');
                    if ($config->get('concrete.cache.full_contents_assets_hash')) {
                        $useFileContents = true;
                    }
                }
                if ($useFileContents) {
                    $sha1 = @sha1_file($filename);
                    if ($sha1 !== false) {
                        $result = $sha1;
                    }
                } else {
                    $mtime = @filemtime($filename);
                    if ($mtime !== false) {
                        $result .= '@' . $mtime;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::setAssetVersion()
     */
    public function setAssetVersion($version)
    {
        $this->assetVersion = $version;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::getAssetVersion()
     */
    public function getAssetVersion()
    {
        return $this->assetVersion;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::setPackageObject()
     */
    public function setPackageObject($pkg)
    {
        $this->pkg = $pkg;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::getAssetContents()
     */
    public function getAssetContents()
    {
        $result = @file_get_contents($this->getAssetPath());

        return ($result === false) ? null : $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::setCombinedAssetSourceFiles()
     */
    public function setCombinedAssetSourceFiles($paths)
    {
        $this->combinedAssetSourceFiles = $paths;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::register()
     */
    public function register($filename, $args, $pkg = false)
    {
        $args += [
            'local' => false,
            'minify' => null,
            'combine' => null,
            'version' => null,
            'position' => null,
        ];
        if ($pkg) {
            if (is_string($pkg)) {
                $app = Application::getFacadeApplication();
                $pkg = $app->make(PackageService::class)->getByHandle($pkg);
            }
            $this->setPackageObject($pkg ?: null);
        }
        $this->setAssetIsLocal($args['local']);
        $this->setAssetLocation($filename);
        if ($args['minify'] === true || $args['minify'] === false) {
            $this->setAssetSupportsMinification($args['minify']);
        }
        if ($args['combine'] === true || $args['combine'] === false) {
            $this->setAssetSupportsCombination($args['combine']);
        }
        if ($args['version']) {
            $this->setAssetVersion($args['version']);
        }
        if ($args['position']) {
            $this->setAssetPosition($args['position']);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::process()
     */
    public static function process($assets)
    {
        return $assets;
    }

    /**
     * Get an AssetPointer instance that identifies this asset.
     *
     * @return \Concrete\Core\Asset\AssetPointer
     */
    public function getAssetPointer()
    {
        $pointer = new AssetPointer($this->getAssetType(), $this->getAssetHandle());

        return $pointer;
    }

    /**
     * Get the contents of an asset given its route.
     *
     * @param string $route
     *
     * @return string|null
     */
    protected static function getAssetContentsByRoute($route)
    {
        $result = null;
        try {
            $app = Application::getFacadeApplication();
            $router = $app->make(RouterInterface::class);
            $routes = $router->getList();
            $context = new RequestContext();
            $request = $app->make(Request::class);
            $context->fromRequest($request);
            $matcher = new UrlMatcher($routes, $context);
            $matched = null;
            try {
                $matched = $matcher->match($route);
            } catch (Exception $x) {
                // Route matcher requires that paths ends with a slash
                if (preg_match('/^(.*[^\/])($|\?.*)$/', $route, $m)) {
                    try {
                        $matched = $matcher->match($m[1] . '/' . (isset($m[2]) ? $m[2] : ''));
                    } catch (Exception $x) {
                    }
                }
            }
            if ($matched !== null) {
                $callable = null;
                $controller = $matched['_controller'];
                if (is_string($controller)) {
                    $chunks = explode('::', $controller, 2);
                    if (count($chunks) === 2) {
                        if (class_exists($chunks[0])) {
                            $array = [$app->make($chunks[0]), $chunks[1]];
                            if (is_callable($array)) {
                                $callable = $array;
                            }
                        }
                    } else {
                        if (class_exists($controller) && method_exists($controller, '__invoke')) {
                            $callable = $app->make($controller);
                        }
                    }
                } elseif (is_callable($controller)) {
                    $callable = $controller;
                }
                if ($callable !== null) {
                    ob_start();
                    $r = call_user_func($callable, false);
                    if ($r instanceof Response) {
                        $result = $r->getContent();
                    } elseif ($r !== false) {
                        $result = ob_get_contents();
                    }
                    ob_end_clean();
                }
            }
        } catch (Exception $x) {
        }

        return $result;
    }
}
