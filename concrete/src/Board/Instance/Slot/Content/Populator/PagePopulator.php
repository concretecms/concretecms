<?php
namespace Concrete\Core\Board\Instance\Slot\Content\Populator;

use Concrete\Core\Board\Instance\Item\Data\DataInterface;
use Concrete\Core\Board\Instance\Item\Data\PageData;
use Concrete\Core\Board\Instance\Logger\Logger;
use Concrete\Core\Board\Instance\Logger\LoggerInterface;
use Concrete\Core\Board\Instance\Slot\Content\SummaryObjectCreatorTrait;
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
     * @param Logger|null $logger
     * @return array
     */
    public function createContentObjects(DataInterface $data, LoggerInterface $logger): array
    {
        $page = Page::getByID($data->getPageID(), 'ACTIVE');
        if ($page && !$page->isError()) {
            return $this->createSummaryContentObjects($page, $logger);
        }
        return [];
    }

}
