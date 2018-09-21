<?php

namespace Concrete\Core\Asset;

use Concrete\Core\Support\Facade\Application;
use Localization;
use URL;

class JavascriptLocalizedAsset extends JavascriptAsset
{
    /**
     * @var bool
     */
    protected $assetSupportsMinification = false;

    /**
     * @return string
     */
    public function getAssetType()
    {
        return 'javascript-localized';
    }

    public function getOutputAssetType()
    {
        return 'javascript';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Asset\Asset::mapAssetLocation()
     */
    public function mapAssetLocation($path)
    {
        $url = URL::to('/' . ltrim($path, '/'))->getRelativeUrl();
        $this->setAssetURL($url);
    }

    /**
     * @return string
     */
    public function getAssetHashKey()
    {
        return $this->assetURL . '::' . Localization::activeLocale() . '::' . sha1($this->getAssetContents());
    }

    public function isAssetLocal()
    {
        return false;
    }

    /**
     * @return string|null
     */
    public function getAssetContents()
    {
        $app = Application::getFacadeApplication();
        $assetRoute = $this->getAssetURL();
        $relativePath = $app->make('app_relative_path');
        if ($relativePath !== '' && strpos($assetRoute, $relativePath . '/') === 0) {
            $assetRoute = substr($assetRoute, strlen($relativePath));
        }
        $prefix = '/' . DISPATCHER_FILENAME . '/';
        if (strpos($assetRoute, $prefix) === 0) {
            $assetRoute = substr($assetRoute, strlen($prefix) - 1);
        }

        return parent::getAssetContentsByRoute($assetRoute);
    }
}
