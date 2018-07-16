<?php
namespace Concrete\Core\Form\Service\Widget;

use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Site\Tree\TreeInterface;
use Core;
use Page;
use Permissions;

class SiteSelector
{
    /**
     * Creates form fields and JavaScript page chooser for choosing a site.
     * <code>
     *     $dh->selectSite('pageID', '1'); // Selects the default site.
     * </code>.
     *
     * @param $fieldName
     * @param bool|int $cID
     *
     * @return string
     */
    public function selectSite($fieldName, $siteID = false, $includeCurrent = false, $includeAll = false)
    {
        $v = \View::getInstance();
        $v->requireAsset('selectize');
        $currentSelected = $siteID !== 'current' ? 'selected' : '';
        $allSelected = $siteID === 'all' ? 'selected' : '';
        $current = t('Current Site');
        $all  = t('All Sites');
        $defaults = t('Default');
        $specific = t('Sites');

        $sites = '';
        foreach(\Core::make('site')->getList() as $site) {
            $sp = new \Permissions($site);
            if ($sp->canViewSiteInSelector()) {
                $sites .= '<option ' . ($site->getSiteID() == $siteID ? 'selected' : '') . ' value="' . $site->getSiteID() . '">' . $site->getSiteName() . '</option>';
            }
        }

        $currentLine = '';
        $allLine = '';
        if ($includeCurrent) {
            $currentLine = "<option value=\"current\" {$currentSelected}>{$current}</option>";
        }
        if ($includeAll) {
            $allLine = "<option value=\"all\" {$allSelected}>{$all}</option>";
        }

        if (!$includeAll && !$includeCurrent) {
            $html = <<<EOL
        <select name="siteID" data-select="search-sites">
            {$sites}
        </select>
EOL;

        } else {

        $html = <<<EOL
        <select name="siteID" data-select="search-sites">
            <optgroup label="{$defaults}">
            {$currentLine}
            {$allLine}
            </optgroup>
            <optgroup label="{$specific}">
                {$sites}
            </optgroup>
        </select>
EOL;

        }

        $html .= <<<EOL
        <script type="text/javascript">$(function() { $('select[data-select=search-sites]').selectize(); });</script>
EOL;

        return $html;
    }


}
