<?php
namespace Concrete\Core\Asset;

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

    abstract public function minify($assets);

    abstract public function combine($assets);

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
}
