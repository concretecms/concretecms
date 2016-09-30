<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Feature\Feature;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Category;

class ImportSinglePageContentRoutine extends AbstractPageContentRoutine
{
    public function getHandle()
    {
        return 'single_page_content';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->singlepages)) {
            foreach ($sx->singlepages->page as $px) {
                if ($px['custom-path']) {
                    $page = Page::getByPath((string) $px['custom-path'], 'RECENT');
                } else {
                    $page = Page::getByPath((string) $px['path'], 'RECENT');
                }
                if (isset($px->area)) {
                    $this->importPageAreas($page, $px);
                }
                if ($page->isError()) {
                    print $px['path'];
                    exit;
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
