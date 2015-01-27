<?php
namespace Concrete\Core\Html\Service;

use Concrete\Core\Asset\CSSAsset;
use Concrete\Core\Asset\JavascriptAsset;
use View;

/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions to help with using HTML. Does not include form elements - those have their own helper.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
class Html
{

    public function css($file, $pkgHandle = null)
    {
        $asset = new CSSAsset();
        // if the first character is a / then that means we just go right through, it's a direct path
        if (substr($file, 0, 4) == 'http' || substr($file, 0, 2) == '//' || strpos($file, '?') > 0) {
            // we can't cache this file, so we make sure to say it's not local. It may BE local –but we can't cache it.
            $asset->setAssetURL($file);
            $asset->setAssetIsLocal(false);
        } else {
            if (substr($file, 0, 1) == '/') {
                $asset->setAssetURL($file);
                // if we're in a relative directory, strip the relative part of the $file, since it'll
                // duplicate in DIR_BASE
                if (DIR_REL != '') {
                    $file = substr($file, strlen(DIR_REL));
                }
                $asset->setAssetPath(DIR_BASE . $file);
            } else {
                $v = View::getInstance();
                // checking the theme directory for it. It's just in the root.
                if ($v instanceof View && $v->getThemeDirectory() != '' && file_exists(
                        $v->getThemeDirectory() . '/' . $file
                    )
                ) {
                    $asset->setAssetURL($v->getThemePath() . '/' . $file);
                    $asset->setAssetPath($v->getThemeDirectory() . '/' . $file);
                } else {
                    if (file_exists(DIR_APPLICATION . '/' . DIRNAME_CSS . '/' . $file)) {
                        $asset->setAssetURL(REL_DIR_APPLICATION . '/' . DIRNAME_CSS . '/' . $file);
                        $asset->setAssetPath(DIR_APPLICATION . '/' . DIRNAME_CSS . '/' . $file);
                    } else {
                        if ($pkgHandle != null) {
                            if (file_exists(DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file)) {
                                $asset->setAssetURL(
                                    REL_DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file
                                );
                                $asset->setAssetPath(DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file);
                            } else {
                                if (file_exists(
                                    DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file
                                )
                                ) {
                                    $asset->setAssetURL(
                                        ASSETS_URL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file
                                    );
                                    $asset->setAssetPath(
                                        DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!$asset->getAssetURL()) {
            $asset->setAssetURL(ASSETS_URL_CSS . '/' . $file);
            $asset->setAssetPath(DIR_BASE_CORE . '/' . DIRNAME_CSS . '/' . $file);
        }
        return $asset;
    }

    public function javascript($file, $pkgHandle = null)
    {
        $asset = new JavascriptAsset();
        // if the first character is a / then that means we just go right through, it's a direct path
        if (substr($file, 0, 4) == 'http' || substr($file, 0, 2) == '//' || strpos($file, '?') > 0) {
            // we can't cache this file, so we make sure to say it's not local. It may BE local –but we can't cache it.
            $asset->setAssetURL($file);
            $asset->setAssetIsLocal(false);
        } else {
            if (substr($file, 0, 1) == '/') {
                $asset->setAssetURL($file);
                // if we're in a relative directory, strip the relative part of the $file, since it'll
                // duplicate in DIR_BASE
                if (DIR_REL != '') {
                    $file = substr($file, strlen(DIR_REL));
                }
                $asset->setAssetPath(DIR_BASE . $file);
            } else {
                if (file_exists(DIR_APPLICATION . '/' . DIRNAME_JAVASCRIPT . '/' . $file)) {
                    $asset->setAssetURL(REL_DIR_APPLICATION . '/' . DIRNAME_JAVASCRIPT . '/' . $file);
                    $asset->setAssetPath(DIR_APPLICATION . '/' . DIRNAME_JAVASCRIPT . '/' . $file);
                } else {
                    if ($pkgHandle != null) {
                        if (file_exists(DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file)) {
                            $asset->setAssetURL(
                                REL_DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file
                            );
                            $asset->setAssetPath(
                                DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file
                            );
                        } else {
                            if (file_exists(
                                DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file
                            )
                            ) {
                                $asset->setAssetURL(
                                    ASSETS_URL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file
                                );
                                $asset->setAssetPath(
                                    DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file
                                );
                            }
                        }
                    }
                }
            }
        }

        if (!$asset->getAssetURL()) {
            $asset->setAssetURL(ASSETS_URL_JAVASCRIPT . '/' . $file);
            $asset->setAssetPath(DIR_BASE_CORE . '/' . DIRNAME_JAVASCRIPT . '/' . $file);
        }
        return $asset;
    }

    /**
     * Takes in a string, and adds rel="nofollow" to any a tags that contain an href attribute
     * @param string $input
     * @return string
     */
    public function noFollowHref($input)
    {
        return preg_replace_callback(
            '/(?:<a(.*?href.*?)>)/i',
            function ($matches) {
                if (strpos($matches[1], 'rel="nofollow"') === false) {
                    //if there is no nofollow add it
                    return '<a' . $matches[1] . ' rel="nofollow">';
                } else {
                    //if there is already a nofollow take no action
                    return $matches[0];
                }
            },
            $input
        );
    }

}
