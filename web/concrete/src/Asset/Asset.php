<?php
namespace Concrete\Core\Asset;

use Environment;

abstract class Asset
{

    protected $assetVersion = '0';
    protected $assetHandle;
    protected $local = true;
    protected $assetURL;
    protected $assetPath;
    protected $assetSupportsMinification = false;
    protected $assetSupportsCombination = false;
    protected $pkg;
    protected $combinedAssetSourceFiles = array();

    const ASSET_POSITION_HEADER = 'H';
    const ASSET_POSITION_FOOTER = 'F';

    abstract public function getAssetDefaultPosition();

    abstract public function getAssetType();

    abstract public function minify($assets);

    abstract public function combine($assets);

    abstract public function __toString();

    public function assetSupportsMinification()
    {
        return $this->local && $this->assetSupportsMinification;
    }

    public function assetSupportsCombination()
    {
        return $this->local && $this->assetSupportsCombination;
    }

    public function setAssetSupportsMinification($minify)
    {
        $this->assetSupportsMinification = $minify;
    }

    public function setAssetSupportsCombination($combine)
    {
        $this->assetSupportsCombination = $combine;
    }

    public function getAssetURL()
    {
        return $this->assetURL;
    }

    public function getAssetPath()
    {
        return $this->assetPath;
    }

    public function getAssetHandle()
    {
        return $this->assetHandle;
    }

    public function __construct($assetHandle = false)
    {
        $this->assetHandle = $assetHandle;
        $this->position = $this->getAssetDefaultPosition();
    }

    public function getAssetFilename()
    {
        return $this->filename;
    }

    public function setAssetVersion($version)
    {
        $this->assetVersion = $version;
    }

    public function setCombinedAssetSourceFiles($paths)
    {
        $this->combinedAssetSourceFiles = $paths;
    }

    public function getAssetVersion()
    {
        return $this->assetVersion;
    }

    public function setAssetPosition($position)
    {
        $this->position = $position;
    }

    public function setPackageObject($pkg)
    {
        $this->pkg = $pkg;
    }

    public function setAssetURL($url)
    {
        $this->assetURL = $url;
    }

    public function setAssetPath($path)
    {
        $this->assetPath = $path;
    }

    public function getAssetURLPath()
    {
        return substr($this->getAssetURL(), 0, strrpos($this->getAssetURL(), '/'));
    }

    public function isAssetLocal()
    {
        return $this->local;
    }

    public function setAssetIsLocal($isLocal)
    {
        $this->local = $isLocal;
    }

    public function getAssetPosition()
    {
        return $this->position;
    }

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
