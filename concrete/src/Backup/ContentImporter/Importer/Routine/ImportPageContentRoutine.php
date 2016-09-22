<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportPageContentRoutine extends AbstractPageContentRoutine
{
    public function getHandle()
    {
        return 'page_content';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->pages)) {
            foreach ($sx->pages->page as $px) {
                if ($px['path'] != '') {
                    $page = Page::getByPath($px['path'], 'RECENT');
                } else {
                    $page = Page::getByID(HOME_CID, 'RECENT');
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
