<?php

namespace Concrete\Core\Multilingual\Service\UserInterface;
use Concrete\Core\Multilingual\Page\Section\Section;
use Database;

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
                $icon = ASSETS_URL . '/' . DIRNAME_IMAGES . '/' . DIRNAME_IMAGES_LANGUAGES . '/' . $region . '.png';
            }

            if (isset($icon)) {
                if ($filePathOnly) {
                    return $icon;
                } else {
                    return '<img class="ccm-region-flag img-reponsive" id="ccm-region-flag-' . $region . '" src="' . $icon . '" alt="' . $region . '" />';
                }
            }
        }
    }

    public function getSectionFlagIcon($page, $filePathOnly = false)
    {
        $db = Database::get();
        $icon = $db->GetOne('select msCountry from MultilingualSections where cID = ?', array($page->getCollectionID()));
        return self::getFlagIcon($icon, $filePathOnly);
    }

    public function getDashboardSitemapIconSRC($page)
    {
        $ids = Section::getIDList();
        if (in_array($page->getCollectionID(), $ids)) {
            return self::getSectionFlagIcon($page, true);
        }
    }

}