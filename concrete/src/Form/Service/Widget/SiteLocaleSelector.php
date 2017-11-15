<?php
namespace Concrete\Core\Form\Service\Widget;

use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\LocaleEntry;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\StandardTreeCollection;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionJsonFormatter;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Permission\Checker;

class SiteLocaleSelector
{
    /**
     * Creates form fields and JavaScript page chooser for choosing a page. For use with inclusion in blocks.
     * <code>
     *     $dh->selectPage('pageID', '1'); // prints out the home page and makes it selectable.
     * </code>.
     *
     * @param $fieldName
     * @param bool|int $cID
     *
     * @return string
     */
    public function selectLocaleTree($fieldName, Site $site, $selectedLocale = null)
    {
        $v = \View::getInstance();
        $v->requireAsset('selectize');

        $collection = new StandardTreeCollection();
        $i = 0;
        foreach($site->getLocales() as $locale) {
            $home = $locale->getSiteTreeObject()->getSiteHomePageObject();
            if ($home) {
                $cp = new Checker($home);
                if ($cp->canViewPageInSitemap()) {
                    $entry = new LocaleEntry($locale);
                    if ($selectedLocale) {
                        if ($selectedLocale->getLocaleID() == $locale->getLocaleID()) {
                            $entry->setIsSelected(true);
                        }
                    } else if ($i == 0) {
                        $entry->setIsSelected(true);
                    }
                    $collection->addEntry($entry);
                }
            }
            $i++;
        }

        $identifier = new \Concrete\Core\Utility\Service\Identifier();
        $identifier = $identifier->getString(32);

        $formatter = new TreeCollectionJsonFormatter($collection);
        $localeTree = json_encode($formatter);

        $html = <<<EOL
        <select data-locale-selector="{$identifier}" style="width: 200px; display: none"></select>
        <script type="text/javascript">
        $(function() {
            var localeTree = {$localeTree},
                itemIDs = [];

            $.each(localeTree.entries, function(i, entry) {
                if (entry.isSelected) {
                    itemIDs.push(entry.siteTreeID);
                }
            });
            $('select[data-locale-selector={$identifier}]').selectize({
                maxItems: 1,
                valueField: 'siteTreeID',
                searchField: 'title',
                options: localeTree.entries,
                items: itemIDs,
                render: {
                    option: function(data, escape) {
                        return '<div class="option">' + data.element + '</div>';
                    },
                    item: function(data, escape) {
                        return '<div class="item">' + data.element + '</div>';
                    }
                }
            });
        });
        </script>
EOL;

        return $html;
    }


}
