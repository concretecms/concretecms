<?php
namespace Concrete\Core\Application\Service;

use Concrete\Core\Asset\CssAsset;
use Concrete\Core\Asset\JavascriptAsset;
use Concrete\Core\Entity\Package;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;

/**
 * \@package Helpers
 *
 * @category Concrete
 *
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
class Urls
{
    /**
     * Gets a full URL to an icon for a particular application.
     *
     * @param \Package $pkg
     *
     * @return string URL to the package's icon
     */
    public function getPackageIconURL($pkg)
    {
        if ($pkg && file_exists($pkg->getPackagePath() . '/' . FILENAME_BLOCK_ICON)) {
            return $this->getPackageURL($pkg) . '/' . FILENAME_BLOCK_ICON;
        } else {
            return PACKAGE_GENERIC_ICON;
        }
    }

    /**
     * Get the package's URL.
     *
     * @param \Package $pkg
     *
     * @return mixed
     */
    public function getPackageURL($pkg)
    {
        return $pkg->getRelativePath();
    }

    /**
     * Gets a full URL to an icon for a particular block type.
     *
     * @param \Concrete\Core\Entity\Block\BlockType\BlockType $bt
     *
     * @return string
     */
    public function getBlockTypeIconURL($bt)
    {
        $url = $this->getBlockTypeAssetsURL($bt, FILENAME_BLOCK_ICON);
        if ($url != false) {
            return $url;
        } else {
            return BLOCK_TYPE_GENERIC_ICON;
        }
    }

    /**
     * Gets a full URL to the directory containing all of a block's items, including JavaScript, tools, icons, etc...
     *
     * @param \Concrete\Core\Entity\Block\BlockType\BlockType $bt
     * @param bool|string $file If provided will get the assets url for a file in a block
     *
     * @return string $url
     */
    public function getBlockTypeAssetsURL($bt, $file = false)
    {
        $ff = '';
        if ($file != false) {
            $ff = '/' . $file;
        }
        $url = '';
        $packageHandle = null;

        if (file_exists(DIR_FILES_BLOCK_TYPES . '/' . $bt->getBlockTypeHandle() . $ff)) {
            $url = REL_DIR_APPLICATION . '/' . DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . $ff;
        } elseif ($bt->getPackageID() > 0) {
            $packageHandle = $bt->getPackageHandle();
            $dirp = (is_dir(DIR_PACKAGES . '/' . $packageHandle)) ? DIR_PACKAGES . '/' . $packageHandle : DIR_PACKAGES_CORE . '/' . $packageHandle;
            if (file_exists($dirp . '/' . DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . $ff)) {
                $url = (is_dir(DIR_PACKAGES . '/' . $packageHandle)) ? DIR_REL : ASSETS_URL;
                $url = $url . '/' . DIRNAME_PACKAGES . '/' . $packageHandle . '/' . DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . $ff;
            }
        } elseif (file_exists(DIR_FILES_BLOCK_TYPES_CORE . '/' . $bt->getBlockTypeHandle() . $ff)) {
            $url = ASSETS_URL . '/' . DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . $ff;
        }
        if ($url && $file) {
            $asset = null;
            if (substr($file, -3) === '.js') {
                $asset = new JavascriptAsset('');
            } elseif (substr($file, -3) === '.css') {
                $asset = new CssAsset('');
            }
            if ($asset !== null) {
                $asset->setAssetIsLocal(true);
                $asset->setAssetURL($url);
                if ($packageHandle) {
                    $app = Application::getFacadeApplication();
                    $em = $app->make(EntityManagerInterface::class);
                    $repo = $em->getRepository(Package::class);
                    $asset->setPackageObject($repo->findOneBy(['pkgHandle' => $packageHandle]));
                }
                $url = $asset->getAssetURL();
            }
        }

        return $url;
    }

    /**
     * Get the URL of the "auto.js" file of a block type, to be loaded when adding/editing a block.
     *
     * @param \BlockType $bt
     *
     * @return string $url Empty string if the auto.js file doesn't exist
     */
    public function getBlockTypeJavaScriptURL($bt)
    {
        return $this->getBlockTypeAssetsURL($bt, 'auto.js');
    }

    /**
     * Get the URL of the "auto.css" file of a block type, to be loaded when adding/editing a block.
     *
     * @param \BlockType $bt
     *
     * @return string $url Empty string if the auto.css file doesn't exist
     *
     * @since concrete5 8.5.0a3
     */
    public function getBlockTypeCssURL($bt)
    {
        return $this->getBlockTypeAssetsURL($bt, 'auto.css');
    }
}
