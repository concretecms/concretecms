<?php
namespace Concrete\Core\Board\DataSource\Populator;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\Configuration\PageConfiguration;
use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;
use Concrete\Core\Entity\Board\Item;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\Summary\Template\RendererFilterer;

defined('C5_EXECUTE') or die("Access Denied.");

class PagePopulator extends AbstractPopulator
{
    
    /**
     * @param Board $board
     * @param PageConfiguration $configuration
     * @return Page[]
     */
    public function getDataSourceObjects(Board $board, Configuration $configuration): array
    {
        $list = new PageList();
        $query = $configuration->getQuery();
        $list->ignorePermissions();
        if ($query) {
            foreach($query->getFields() as $field) {
                $field->filterList($list);
            }
        }
        if ($board->getDateLastUpdated()) {
            $filterDate = date('Y-m-d H:i:s', $board->getDateLastUpdated());
            $list->filterByPublicDate($filterDate, '>');
        }

        $list->setItemsPerPage(100);
        return $list->getResults();
    }

    /**
     * @param Page $mixed
     * @return int
     */
    public function getObjectRelevantDate($mixed): int
    {
        return $mixed->getCollectionDatePublicObject()->getTimestamp();
    }
    
}
