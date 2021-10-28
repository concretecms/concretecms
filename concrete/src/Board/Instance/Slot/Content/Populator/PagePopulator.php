<?php
namespace Concrete\Core\Board\Instance\Slot\Content\Populator;

use Concrete\Core\Board\Instance\Slot\Content\ObjectInterface;
use Concrete\Core\Board\Instance\Slot\Content\SummaryObjectCreatorTrait;
use Concrete\Core\Board\Instance\Item\Data\DataInterface;
use Concrete\Core\Board\Instance\Item\Data\PageData;
use Concrete\Core\Page\Page;

defined('C5_EXECUTE') or die("Access Denied.");

class PagePopulator extends AbstractPopulator
{

    use SummaryObjectCreatorTrait;

    public function getDataClass(): string
    {
        return PageData::class;
    }

    /**
     * @param PageData $data
     * @return ObjectInterface[]
     */
    public function createContentObjects(DataInterface $data): array
    {
        $page = Page::getByID($data->getPageID(), 'ACTIVE');
        if ($page && !$page->isError()) {
            return $this->createSummaryContentObjects($page);
        }
    }

}
