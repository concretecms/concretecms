<?php
namespace Concrete\Core\Api\Fractal\Transformer\Traits;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Page\Page;
use Concrete\Core\Area\ApiArea;

trait GetPageApiAreasTrait
{

    public function getAreas(Page $page): array
    {
        /**
         * @var $db Connection
         */
        $db = app(Connection::class);
        $r = $db->createQueryBuilder()
            ->select("distinct cvb.arHandle")
            ->from("CollectionVersionBlocks", "cvb")
            ->where("cvb.cID = :pageId AND cvb.cvID = :pageVersionId")
            ->orderBy('arHandle')
            ->setParameter("pageId", $page->getCollectionID())
            ->setParameter("pageVersionId", $page->getVersionID())
            ->execute();

        $areas = [];
        while ($row = $r->fetchAssociative()) {
            $areas[] = new ApiArea($page, $row['arHandle']);
        }
        return $areas;
    }
}
