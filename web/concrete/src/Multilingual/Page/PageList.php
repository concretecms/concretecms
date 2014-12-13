<?php

namespace Concrete\Core\Multilingual\Page;

use Concrete\Core\Page\PageList as CorePageList;
use Concrete\Core\Multilingual\Page\Section;
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
            $query->addSelect('(' . $section . ')');
        }
        return $query;
    }

    public function filterByMissingTargets($targets)
    {
        /*
        $haveStr = '';

        if (count($targets) > 0) {
            $haveStr .= '(';
        }

        for ($i = 0; $i < count($targets); $i++) {
            $t = $targets[$i];
            $haveStr .= 'relationCount' . $t->getCollectionID() . ' = 0';
            if (count($targets) > ($i + 1)) {
                $haveStr .= ' or ';
            }
        }

        if (count($targets) > 0) {
            $haveStr .= ')';
        }

        if ($haveStr) {
            $this->having(false, $haveStr);
        }
        */
    }
}
