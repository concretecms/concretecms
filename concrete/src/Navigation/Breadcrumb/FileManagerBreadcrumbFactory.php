<?php

namespace Concrete\Core\Navigation\Breadcrumb;

use Concrete\Core\Entity\Search\SavedFileSearch;
use Concrete\Core\Navigation\Breadcrumb\Dashboard\DashboardBreadcrumb;
use Concrete\Core\Navigation\Item\FileFolderItem;
use Concrete\Core\Navigation\Item\SavedSearchItem;
use Concrete\Core\Tree\Node\Type\FileFolder;

class FileManagerBreadcrumbFactory
{
    public function getBreadcrumb(object $mixed): BreadcrumbInterface
    {
        $breadcrumb = new DashboardBreadcrumb();
        if ($mixed instanceof FileFolder) {
            if ($mixed->getTreeNodeParentID() > 0) {
                $nodes = array_reverse($mixed->getTreeNodeParentArray());

                /**
                 * @var FileFolder[]
                 */
                foreach ($nodes as $node) {
                    $item = new FileFolderItem($node);

                    $breadcrumb->add($item);
                }
            }

            $item = new FileFolderItem($mixed);

            $breadcrumb->add($item);
        } elseif ($mixed instanceof SavedFileSearch) {
            $item = new SavedSearchItem($mixed);

            $breadcrumb->add($item);
        }

        return $breadcrumb;
    }
}
