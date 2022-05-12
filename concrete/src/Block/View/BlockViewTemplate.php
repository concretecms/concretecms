<?php
namespace Concrete\Core\Block\View;

use Loader;
use AssetList;
use View;
use Concrete\Core\Block\Block;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Asset\JavascriptAsset;
use Concrete\Core\Asset\CssAsset;
use Concrete\Core\Filesystem\FileLocator;

class BlockViewTemplate
{
    protected $basePath = '';

    protected $bFilename;
    protected $btHandle;
    protected $obj;
    protected $template;
    protected $baseURL;
    protected $checkAssets = true;
    protected $itemsToCheck = array(
        'CSS' => 'view.css',
        'JAVASCRIPT' => 'view.js',
    );
    protected $render = FILENAME_BLOCK_VIEW;
    /**
     * @var PackageList
     */
    protected $packageList;
    protected $theme;

    public function __construct($obj, PackageList $packageList = null)
    {
        $this->btHandle = $obj->getBlockTypeHandle();
        $this->obj = $obj;
        if ($obj instanceof Block) {
            $this->bFilename = $obj->getBlockFilename();
            $c = $obj->getBlockCollectionObject();
            if (is_object($c)) {
                $this->theme = $c->getCollectionThemeObject();
            }
        } else {
            $c = \Page::getCurrentPage();
            if ($c) {
                $this->theme = $c->getCollectionThemeObject();
            }
        }
        if ($packageList) {
            $this->setPackageList($packageList);
        } else {
            $this->setPackageList(PackageList::get());
        }
        $this->computeView();
    }

    /**
     * @return PackageList
     */
    public function getPackageList()
    {
        return $this->packageList;
    }

    /**
     * @param static $packageList
     */
    public function setPackageList($packageList)
    {
        $this->packageList = $packageList;
    }


    protected function computeView()
    {
        $bFilename = $this->bFilename ?? '';
        $obj = $this->obj;

        /**
         * @var $locator FileLocator
         */
        $locator = \Core::make(FileLocator::class);
        if (is_object($this->theme)) {
            $locator->addLocation(new FileLocator\ThemeLocation($this->theme));
        }
        if ($obj->getPackageHandle()) {
            $locator->addLocation(new FileLocator\PackageLocation($obj->getPackageHandle(), true));
        }
        $locator->addLocation(new FileLocator\AllPackagesLocation($this->getPackageList()));

        // if we've passed in "templates/" as the first part, we strip that off.
        if (strpos($bFilename, 'templates/') === 0) {
            $bFilename = substr($bFilename, 10);
        }

        // The filename might be a directory name with .php-appended (BlockView does that), strip it.
        $bFilenameWithoutDotPhp = $bFilename;
        if (substr($bFilename, -4) === ".php") {
            $bFilenameWithoutDotPhp = substr($bFilename, 0, strlen($bFilename) - 4);
        }

        if ($bFilename) {
            $record = $locator->getRecord(
                DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename);
            if ($record->exists()) {
                if (is_dir($record->getFile())) {
                    $this->template = $record->getFile() . '/' . FILENAME_BLOCK_VIEW;
                    $this->baseURL = $record->getUrl();
                    $this->basePath = $record->getFile();
                } else {
                    $this->template = $record->getFile();
                    $record = $locator->getRecord(
                        DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . $this->render
                    );
                    $this->baseURL = dirname($record->getUrl());
                    $this->basePath = dirname($record->getFile());
                }

                return;
            }

            if ($bFilename !== $bFilenameWithoutDotPhp) {
                $record = $locator->getRecord(
                    DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilenameWithoutDotPhp
                );
                if ($record->exists() && is_dir($record->getFile())) {
                    $this->template = $record->getFile() . '/' . $this->render;
                    $this->baseURL = $record->getUrl();
                    $this->basePath = $record->getFile();
                    return;
                }
            }
        }

        $record = $locator->getRecord(
            DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . $this->render
        );
        if ($record->exists()) {
            $this->baseURL = dirname($record->getUrl());
            $this->template = $record->getFile();
            $this->basePath = dirname($this->template);
        }

    }

    public function getBasePath()
    {
        return $this->basePath;
    }
    public function getBaseURL()
    {
        return $this->baseURL;
    }
    public function setBlockCustomTemplate($bFilename)
    {
        $this->bFilename = $bFilename;
        $this->computeView();
    }

    public function setBlockCustomRender($renderFilename)
    {
        // if we've passed in "templates/" as the first part, we strip that off.
        if (strpos($renderFilename, 'templates/') === 0) {
            $bFilename = substr($renderFilename, 10);
            $this->setBlockCustomTemplate($bFilename);
        } else {
            $this->render = $renderFilename;
        }
        $this->computeView();
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function registerTemplateAssets()
    {
        $items = array();
        $h = Loader::helper("html");
        $dh = Loader::helper('file');
        if ($this->checkAssets == false) {
            return $items;
        } else {
            $al = AssetList::getInstance();
            $v = View::getInstance();
            foreach ($this->itemsToCheck as $t => $i) {
                if (file_exists($this->basePath . '/' . $i)) {
                    $identifier = substr($this->basePath, strpos($this->basePath, 'blocks'));
                    // $identifier = 'blocks/page_list', 'blocks/feature', 'blocks/page_list/templates/responsive', etc...
                    $asset = null;
                    switch ($t) {
                        case 'CSS':
                            $asset = new CssAsset($identifier);
                            $asset->setAssetURL($this->getBaseURL() . '/' . $i);
                            $asset->setAssetPath($this->basePath . '/' . $i);
                            $al->registerAsset($asset);
                            $v->requireAsset('css', $identifier);
                            break;
                        case 'JAVASCRIPT':
                            $asset = new JavascriptAsset($identifier);
                            $asset->setAssetURL($this->getBaseURL() . '/' . $i);
                            $asset->setAssetPath($this->basePath . '/' . $i);
                            $al->registerAsset($asset);
                            $v->requireAsset('javascript', $identifier);
                            break;
                    }
                    if ($asset !== null) {
                        if ($this->obj instanceof Block) {
                            $pkgID = $this->obj->getPackageID();
                            if ($pkgID) {
                                foreach ($this->getPackageList()->getPackages() as $pkg) {
                                    if ($pkg->getPackageID() == $pkgID) {
                                        $asset->setPackageObject($pkg);
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $css = $dh->getDirectoryContents($this->basePath . '/' . DIRNAME_CSS);
            $js = $dh->getDirectoryContents($this->basePath . '/' . DIRNAME_JAVASCRIPT);
            if (count($css) > 0) {
                foreach ($css as $i) {
                    if (substr($i, -4) == '.css') {
                        $identifier = substr($this->basePath, strpos($this->basePath, 'blocks')) . '/' . $i;
                        $asset = new CssAsset($identifier);
                        $asset->setAssetURL($this->getBaseURL() . '/' . DIRNAME_CSS . '/' . $i);
                        $asset->setAssetPath($this->basePath . '/' . DIRNAME_CSS . '/' . $i);
                        $al->registerAsset($asset);
                        $v->requireAsset('css', $identifier);
                    }
                }
            }
            if (count($js) > 0) {
                foreach ($js as $i) {
                    if (substr($i, -3) == '.js') {
                        $identifier = substr($this->basePath, strpos($this->basePath, 'blocks')) . '/' . $i;
                        $asset = new JavascriptAsset($identifier);
                        $asset->setAssetURL($this->getBaseURL() . '/' . DIRNAME_JAVASCRIPT . '/' . $i);
                        $asset->setAssetPath($this->basePath . '/' . DIRNAME_JAVASCRIPT . '/' . $i);
                        $al->registerAsset($asset);
                        $v->requireAsset('javascript', $identifier);
                    }
                }
            }
        }
    }
}
