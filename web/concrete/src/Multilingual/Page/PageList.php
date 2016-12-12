<?php

namespace Concrete\Core\Multilingual\Page;

use Concrete\Core\Page\PageList as CorePageList;
use Concrete\Core\Multilingual\Page\Section\Section;
use Database;
use Doctrine\DBAL\Query\QueryBuilder;

defined('C5_EXECUTE') or die("Access Denied.");

class PageList extends CorePageList
{
    protected $includeAliases = false;

    public function finalizeQuery(QueryBuilder $query)
    {
        $query = parent::finalizeQuery($query);
        $mslist = Section::getList();
        $relation = Database::get()->createQueryBuilder();
        $relation->select('mpRelationID')->from('MultilingualPageRelations', 'mppr')->where('cID = p.cID')->setMaxResults(1);
        $query->addSelect('(' . $relation . ') as mpr');
        foreach($mslist as $ms) {
            $section = Database::get()->createQueryBuilder();
            $section
                ->select('count(mpRelationID)')
                ->from('MultilingualPageRelations', 'mppr')
                ->where('mpRelationID = mpr')
                ->andWhere(
                    $section->expr()->comparison('mpLocale', '=', $query->createNamedParameter($ms->getLocale()))
                );
            $query->addSelect('(' . $section . ') as relationCount' . $ms->getCollectionID());
        }
        return $query;
    }

    public function filterByMissingTargets($targets)
    {
        if (count($targets)) {
            for ($i = 0; $i < count($targets); $i++) {
                $having = Database::get()->createQueryBuilder();
                $t = $targets[$i];
                $this->query->having(
                    $having->expr()->orX('relationCount' . $t->getCollectionID() . ' = 0')
                );
            }
        }
    }
}
