<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Feature\Feature;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Category;

class ImportSinglePageContentRoutine extends AbstractPageContentRoutine implements SpecifiableHomePageRoutineInterface
{
    public function getHandle()
    {
        return 'single_page_content';
    }

    public function setHomePage($page)
    {
        $this->home = $page;
    }

    public function import(\SimpleXMLElement $sx)
    {
        $siteTree = null;
        if (isset($this->home)) {
            $siteTree = $this->home->getSiteTreeObject();
        }

        if (isset($sx->singlepages)) {
            foreach ($sx->singlepages->page as $px) {
                if ($px['custom-path']) {
                    $page = Page::getByPath((string) $px['custom-path'], 'RECENT', $siteTree);
                } else {
                    $page = Page::getByPath((string) $px['path'], 'RECENT', $siteTree);
                }

                if (isset($px->area)) {
                    $this->importPageAreas($page, $px);
                }

                if (isset($px->attributes)) {
                    foreach ($px->attributes->children() as $attr) {
                        $ak = CollectionKey::getByHandle($attr['handle']);
                        if (is_object($ak)) {
                            $page->setAttribute((string) $attr['handle'], $ak->getController()->importValue($attr));
                        }
                    }
                }
            }
        }
    }
}
