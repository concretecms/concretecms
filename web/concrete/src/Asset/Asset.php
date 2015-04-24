<?php

namespace Concrete\Core\Asset;

use Concrete\Core\Package\Package;
use Environment;

abstract class Asset
{
    /**
     * @var string
     */
    protected $assetVersion = '0';

    /**
     * @var string
     */
    protected $assetHandle;

    /**
     * @var bool
     */
    protected $local = true;

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $assetURL;

    /**
     * @var string
     */
    protected $assetPath;

    /**
     * @var bool
     */
    protected $assetSupportsMinification = false;

    /**
     * @var bool
     */
    protected $assetSupportsCombination = false;

    /**
     * @var \Package
     */
    protected $pkg;

    /**
     * @var array
     */
    protected $combinedAssetSourceFiles = array();

    const ASSET_POSITION_HEADER = 'H';
    const ASSET_POSITION_FOOTER = 'F';

    abstract public function getAssetDefaultPosition();

    abstract public function getAssetType();

    public function getOutputAssetType()
    {
        return $this->getAssetType();
    }

    /**
     * @param Asset[] $assets
     *
     * @return Asset[]
     */
    abstract public static function process($assets);

    abstract public function __toString();

    /**
     * @return bool
     */
    public function assetSupportsMinification()
    {
        return $this->local && $this->assetSupportsMinification;
    }

    /**
     * @return bool
     */
    public function assetSupportsCombination()
    {
        return $this->local && $this->assetSupportsCombination;
    }

    /**
     * @param bool $minify
     */
    public function setAssetSupportsMinification($minify)
    {
        $this->assetSupportsMinification = $minify;
    }

    /**
     * @param bool $combine
     */
    public function setAssetSupportsCombination($combine)
    {
        $this->assetSupportsCombination = $combine;
    }

    /**
     * @return string
     */
    public function getAssetURL()
    {
        return $this->assetURL;
    }

    /**
     * @return string
     */
    public function getAssetHashKey()
    {
        $result = $this->assetURL;
        if ($this->isAssetLocal()) {
            $filename = $this->getAssetPath();
            if (is_file($filename)) {
                $mtime = @filemtime($filename);
                if ($mtime !== false) {
                    $result .= '@' . $mtime;
                }
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getAssetPath()
    {
        return $this->assetPath;
    }

    /**
     * @return string
     */
    public function getAssetHandle()
    {
        return $this->assetHandle;
    }

    /**
     * @param bool|string $assetHandle
     */
    public function __construct($assetHandle = false)
    {
        $this->assetHandle = $assetHandle;
        $this->position = $this->getAssetDefaultPosition();
    }

    /**
     * @return string
     */
    public function getAssetFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $version
     */
    public function setAssetVersion($version)
    {
        $this->assetVersion = $version;
    }

    /**
     * @param array $paths
     */
    public function setCombinedAssetSourceFiles($paths)
    {
        $this->combinedAssetSourceFiles = $paths;
    }

    /**
     * @return string
     */
    public function getAssetVersion()
    {
        return $this->assetVersion;
    }

    /**
     * @param string $position
     */
    public function setAssetPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @param \Package $pkg
     */
    public function setPackageObject($pkg)
    {
        $this->pkg = $pkg;
    }

    /**
     * @param string $url
     */
    public function setAssetURL($url)
    {
        $this->assetURL = $url;
    }

    /**
     * @param string $path
     */
    public function setAssetPath($path)
    {
        $this->assetPath = $path;
    }

    /**
     * @return string
     */
    public function getAssetURLPath()
    {
        return substr($this->getAssetURL(), 0, strrpos($this->getAssetURL(), '/'));
    }

    /**
     * @return bool
     */
    public function isAssetLocal()
    {
        return $this->local;
    }

    /**
     * @param bool $isLocal
     */
    public function setAssetIsLocal($isLocal)
    {
        $this->local = $isLocal;
    }

    /**
     * @return string
     */
    public function getAssetPosition()
    {
        return $this->position;
    }

    /**
     * @param string $path
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
     * @return string|null
     */
    public function getAssetContents()
    {
        $result = @file_get_contents($this->getAssetPath());

        return ($result === false) ? null : $result;
    }

    /**
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
                        $matched = $matcher->match($m[1].'/'.(isset($m[2]) ? $m[2] : ''));
                    } catch (\Exception $x) {
                    }
                }
            }
            if (isset($matched)) {
                $controller = $matched['_controller'];
                if (is_callable($controller)) {
                    ob_start();
                    $r = call_user_func($controller, false);
                    if ($r !== false) {
                        $result = ob_get_contents();
                    }
                    ob_end_clean();
                }
            }
        } catch (\Exception $x) {
        }

        return $result;
    }

    public function register($filename, $args, $pkg = false)
    {
        if ($pkg != false) {
            if (!($pkg instanceof Package)) {
                $pkg = Package::getByHandle($pkg);
            }
            $this->setPackageObject($pkg);
        }
        $this->setAssetIsLocal($args['local']);
        $this->mapAssetLocation($filename);
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
}
