<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportPageContentRoutine extends AbstractPageContentRoutine implements SpecifiableHomePageRoutineInterface
{
    public function getHandle()
    {
        return 'page_content';
    }

    /**
     * Useful when we're calling this from another routine that imports a new home page.
     * @param $c
     */
    public function setHomePage($c)
    {
        $this->home = $c;
    }

    public function import(\SimpleXMLElement $sx)
    {

        $siteTree = null;
        if (isset($this->home)) {
            $siteTree = $this->home->getSiteTreeObject();
        }

        if (isset($sx->pages)) {
            foreach ($sx->pages->page as $px) {
                if ($px['path'] != '') {
                    $page = Page::getByPath($px['path'], 'RECENT', $siteTree);
                } else {
                    if (isset($this->home)) {
                        $page = $this->home;
                    } else {
                        $page = Page::getByID(Page::getHomePageID(), 'RECENT');
                    }
                }
                if (isset($px->area)) {
                    $this->importPageAreas($page, $px);
                }
                if (isset($px->attributes)) {
                    foreach ($px->attributes->children() as $attr) {
                        $handle = (string) $attr['handle'];
                        $ak = CollectionKey::getByHandle($handle);
                        if (is_object($ak)) {
                            $value = $ak->getController()->importValue($attr);
                            $page->setAttribute($handle, $value);
                        }
                    }
                }
                $page->reindex();
            }
        }

    }

}
