<?php

namespace Concrete\Multilingual\Service\UserInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class Flag
{

    /**
     * Returns a flag for a passed country/region
     */
    public function getFlagIcon($region, $filePathOnly = false)
    {
        if ($region) {
            $region = strtolower($region);

            if (file_exists(
                DIR_BASE . '/' . DIRNAME_IMAGES . '/' . DIRNAME_IMAGES_LANGUAGES . '/' . $region . '.png'
            )) {
                $icon = DIR_REL . '/' . DIRNAME_IMAGES . '/' . DIRNAME_IMAGES_LANGUAGES . '/' . $region . '.png';
            } else {
                if (file_exists(
                    DIR_PACKAGES_CORE . '/multilingual/' . DIRNAME_IMAGES . '/' . DIRNAME_IMAGES_LANGUAGES . '/' . $region . '.png'
                )) {
                    $icon = ASSETS_URL . '/' . DIRNAME_PACKAGES . '/multilingual/' . DIRNAME_IMAGES . '/' . DIRNAME_IMAGES_LANGUAGES . '/' . $region . '.png';
                } else {
                    $icon = DIR_REL . '/' . DIRNAME_PACKAGES . '/multilingual/' . DIRNAME_IMAGES . '/' . DIRNAME_IMAGES_LANGUAGES . '/' . $region . '.png';
                }
            }

            if (isset($icon)) {
                if ($filePathOnly) {
                    return $icon;
                } else {
                    return '<img class="ccm-region-flag" id="ccm-region-flag-' . $region . '" width="' . MULTILINGUAL_FLAGS_WIDTH . '" height="' . MULTILINGUAL_FLAGS_HEIGHT . '" src="' . $icon . '" alt="' . $region . '" />';
                }
            }
        }
    }

    public function getSectionFlagIcon($page, $filePathOnly = false)
    {
        $db = Loader::db();
        $icon = $db->GetOne('select msIcon from MultilingualSections where cID = ?', array($page->getCollectionID()));
        return self::getFlagIcon($icon, $filePathOnly);
    }

    public function getDashboardSitemapIconSRC($page)
    {
        $ids = MultilingualSection::getIDList();
        if (in_array($page->getCollectionID(), $ids)) {
            return self::getSectionFlagIcon($page, true);
        }
    }

}