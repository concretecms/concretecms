<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Feature\Feature;
use Concrete\Core\Page\Feed;
use Concrete\Core\Permission\Category;

class ImportPageFeedsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'page_feeds';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->pagefeeds)) {
            foreach ($sx->pagefeeds->feed as $f) {
                $feed = Feed::getByHandle((string) $f->handle);
                $inspector = \Core::make('import/value_inspector');
                if (!is_object($feed)) {
                    $feed = new \Concrete\Core\Entity\Page\Feed();
                }
                if ($f->parent) {
                    $result = $inspector->inspect((string) $f->parent);
                    $parent = $result->getReplacedValue();
                    $feed->setParentID($parent);
                }
                $feed->setTitle((string) $f->title);
                $feed->setDescription((string) $f->description);
                $feed->setHandle((string) $f->handle);
                if ($f->descendents) {
                    $feed->setIncludeAllDescendents(true);
                }
                if ($f->aliases) {
                    $feed->setDisplayAliases(true);
                }
                if ($f->featured) {
                    $feed->setDisplayFeaturedOnly(true);
                }
                if ($f->pagetype) {
                    $result = $inspector->inspect((string) $f->pagetype);
                    $pagetype = $result->getReplacedValue();
                    $feed->setPageTypeID($pagetype);
                }
                $contentType = $f->contenttype;
                $type = (string) $contentType['type'];
                if ($type == 'description') {
                    $feed->displayShortDescriptionContent();
                } elseif ($type == 'area') {
                    $feed->displayAreaContent((string) $contentType['handle']);
                }
                $feed->save();
            }
        }
    }
}
