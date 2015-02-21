<?php
namespace Concrete\Core\Application\Service;

/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

class Urls
{

    /**
     * Gets a full URL to an icon for a particular application
     * @param \Package $pkg
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
     * Get the package's URL
     * @param \Package $pkg
     * @return mixed
     */
    public function getPackageURL($pkg)
    {
        return $pkg->getRelativePath();
    }

    /**
     * @deprecated
     * Gets a URL to reference a script in the tools directory
     * @param string $tool
     * @param string $pkgHandle
     * @return string Relative url to tool
     */
    public function getToolsURL($tool, $pkgHandle = null)
    {
        if ($pkgHandle != null) {
            $url = REL_DIR_FILES_TOOLS_PACKAGES . '/' . $pkgHandle . '/' . $tool;
            return $url;
        } else {
            if (file_exists(DIR_APPLICATION . '/' . DIRNAME_TOOLS . '/' . $tool . '.php')) {
                return REL_DIR_FILES_TOOLS . '/' . $tool;
            } else {
                return REL_DIR_FILES_TOOLS_REQUIRED . '/' . $tool;
            }
        }
    }

    /**
     * Gets a full URL to an icon for a particular block type
     * @param \BlockType $bt
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
     * @param \BlockType $bt
     * @param bool|string $file If provided will get the assets url for a file in a block
     * @return string $url
     */
    public function getBlockTypeAssetsURL($bt, $file = false)
    {
        $ff = '';
        if ($file != false) {
            $ff = '/' . $file;
        }
        $url = '';

        if (file_exists(DIR_FILES_BLOCK_TYPES . '/' . $bt->getBlockTypeHandle() . $ff)) {
            $url = REL_DIR_APPLICATION . '/' . DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . $ff;
        } elseif ($bt->getPackageID() > 0) {
            $h = $bt->getPackageHandle();
            $dirp = (is_dir(DIR_PACKAGES . '/' . $h)) ? DIR_PACKAGES . '/' . $h : DIR_PACKAGES_CORE . '/' . $h;
            if (file_exists($dirp . '/' . DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . $ff)) {
                $url = (is_dir(DIR_PACKAGES . '/' . $h)) ? DIR_REL : ASSETS_URL;
                $url = $url . '/' . DIRNAME_PACKAGES . '/' . $h . '/' . DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . $ff;
            }
        } elseif (file_exists(DIR_FILES_BLOCK_TYPES_CORE . '/' . $bt->getBlockTypeHandle() . $ff)) {
            $url = ASSETS_URL . '/' . DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . $ff;
        }
        return $url;
    }

    /**
     * Gets a full URL to a block's JavaScript file (if one exists)
     * @param \BlockType $bt
     * @return string $url
     */
    public function getBlockTypeJavaScriptURL($bt)
    {
        return $this->getBlockTypeAssetsURL($bt, 'auto.js');
    }

    /**
     * @deprecated
     * Gets a full URL to a block's tools directory
     * @param \BlockType $bt
     * @return string $url
     */
    public function getBlockTypeToolsURL($bt)
    {
        return REL_DIR_FILES_TOOLS_BLOCKS . '/' . $bt->getBlockTypeHandle();
    }


}
