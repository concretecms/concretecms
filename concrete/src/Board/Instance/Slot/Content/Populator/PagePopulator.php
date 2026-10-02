<?php
namespace Concrete\Core\Board\Instance\Slot\Content\Populator;

use Concrete\Core\Board\Instance\Item\Data\DataInterface;
use Concrete\Core\Board\Instance\Item\Data\PageData;
use Concrete\Core\Board\Instance\Logger\LoggerInterface;
use Concrete\Core\Board\Instance\Slot\Content\SummaryObjectCreatorTrait;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

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
     * @param LoggerInterface $logger
     * @param bool $enforceViewPermissions
     * @return array
     */
    public function createContentObjects(DataInterface $data, LoggerInterface $logger, bool $enforceViewPermissions = false): array
    {
        $page = Page::getByID($data->getPageID(), 'ACTIVE');
        if ($page && !$page->isError()) {
            if ($enforceViewPermissions) {
                $checker = new Checker($page);
                if (!$checker->canViewPage()) {
                    return [];
                }
            }
            return $this->createSummaryContentObjects($page, $logger);
        }
        return [];
    }

}
