<?php

namespace Concrete\Core\Asset;

use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Application;
use Environment;
use Symfony\Component\HttpFoundation\Response;

abstract class Asset implements AssetInterface
{
    /**
     * The location of the asset (used to build the path & URL).
     *
     * @var string
     */
    protected $location;

    /**
     * Does the URL/path have already been resolved (starting from the location) for this (local) assets?
     *
     * @var bool
     */
    protected $assetHasBeenMapped = false;

    /**
     * The asset version.
     *
     * @var string
     */
    protected $assetVersion = '0';

    /**
     * The handle of this asset (together with getAssetType, identifies this asset).
     *
     * @var string
     */
    protected $assetHandle;

    /**
     * Is this asset a locally available file (accessible with the getAssetPath method)?
     *
     * @var bool
     */
    protected $local = true;

    /**
     * The name of the file of this asset.
     *
     * @var string
     */
    protected $filename;

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
     * @see \Concrete\Core\Asset\AssetInterface::process()
     */
    public static function process($assets)
    {
        return $assets;
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
     * @see \Concrete\Core\Asset\AssetInterface::assetSupportsMinification()
     */
    public function assetSupportsMinification()
    {
        return $this->local && $this->assetSupportsMinification;
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
     * @see \Concrete\Core\Asset\AssetInterface::setAssetSupportsMinification()
     */
    public function setAssetSupportsMinification($minify)
    {
        $this->assetSupportsMinification = $minify;
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
     * @see \Concrete\Core\Asset\AssetInterface::getAssetHashKey()
     */
    public function getAssetHashKey()
    {
        $result = $this->assetURL;
        if ($this->isAssetLocal()) {
            $filename = $this->getAssetPath();
            if (is_file($filename)) {
                if (is_readable($filename) && \Config::get('concrete.cache.full_contents_assets_hash')) {
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
     * @see \Concrete\Core\Asset\AssetInterface::getAssetHandle()
     */
    public function getAssetHandle()
    {
        return $this->assetHandle;
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
     * @see \Concrete\Core\Asset\AssetInterface::setAssetVersion()
     */
    public function setAssetVersion($version)
    {
        $this->assetVersion = $version;
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
     * @see \Concrete\Core\Asset\AssetInterface::getAssetVersion()
     */
    public function getAssetVersion()
    {
        return $this->assetVersion;
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
     * @see \Concrete\Core\Asset\AssetInterface::setPackageObject()
     */
    public function setPackageObject($pkg)
    {
        $this->pkg = $pkg;
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
     * @see \Concrete\Core\Asset\AssetInterface::setAssetPath()
     */
    public function setAssetPath($path)
    {
        $this->assetHasBeenMapped = true;
        $this->assetPath = $path;
    }

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
     * @see \Concrete\Core\Asset\AssetInterface::isAssetLocal()
     */
    public function isAssetLocal()
    {
        return $this->local;
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
     * @see \Concrete\Core\Asset\AssetInterface::getAssetPosition()
     */
    public function getAssetPosition()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\AssetInterface::mapAssetLocation()
     */
    public function mapAssetLocation($path)
    {
        if ($this->isAssetLocal()) {
            $env = Environment::get();
            $pkgHandle = false;
            if (is_object($this->pkg)) {
                $pkgHandle = $this->pkg->getPackageHandle();
            }
            $r = $env->getRecord($path, $pkgHandle);
            $this->setAssetPath($r->file);
            $this->setAssetURL($r->url);
        } else {
            $this->setAssetURL($path);
        }
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
     * @see \Concrete\Core\Asset\AssetInterface::register()
     */
    public function register($filename, $args, $pkg = false)
    {
        if ($pkg != false) {
            if ($pkg !== false && is_string($pkg)) {
                $pkg = Package::getByHandle($pkg);
            }
            $this->setPackageObject($pkg);
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
            $routes = \Route::getList();
            /* @var $routes \Symfony\Component\Routing\RouteCollection */
            $context = new \Symfony\Component\Routing\RequestContext();
            $request = \Request::getInstance();
            $context->fromRequest($request);
            $matcher = new \Symfony\Component\Routing\Matcher\UrlMatcher($routes, $context);
            $matched = null;
            try {
                $matched = $matcher->match($route);
            } catch (\Exception $x) {
                $m = null;
                // Route matcher requires that paths ends with a slash
                if (preg_match('/^(.*[^\/])($|\?.*)$/', $route, $m)) {
                    try {
                        $matched = $matcher->match($m[1] . '/' . (isset($m[2]) ? $m[2] : ''));
                    } catch (\Exception $x) {
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
                            $array = [Application::getFacadeApplication()->make($chunks[0]), $chunks[1]];
                            if (is_callable($array)) {
                                $callable = $array;
                            }
                        }
                    } else {
                        if (class_exists($controller) && method_exists($controller, '__invoke')) {
                            $callable = Application::getFacadeApplication()->make($controller);
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
        } catch (\Exception $x) {
        }

        return $result;
    }
}
