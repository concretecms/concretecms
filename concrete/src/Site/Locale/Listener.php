<?php

namespace Concrete\Core\Site\Locale;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Site\Locale as LocaleEntity;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class Listener
{
    public function preUpdate(LocaleEntity $locale, PreUpdateEventArgs $args)
    {
        $changeset = $args->getEntityChangeSet();
        if (isset($changeset['msLanguage']) || isset($changeset['msCountry'])) {
            $tree = $locale->getSiteTree();
            if ($tree !== null) {
                $homeCID = (int) $tree->getSiteHomePageID();
                if ($homeCID !== 0) {
                    $cn = $args->getEntityManager()->getConnection();
                    $sqlLanguage = $cn->quote($locale->getLanguage());
                    $sqlLocale = $cn->quote($locale->getLocale());
                    $updateMultilingualRelation = <<<EOT
UPDATE MultilingualPageRelations
SET mpLocale = {$sqlLocale}, mpLanguage = {$sqlLanguage}
WHERE cID IN (:cIDs:)
EOT
                    ;
                    $this->fixMultilingualPageRelations($cn, $updateMultilingualRelation, [$homeCID]);
                }
            }
        }
    }

    /**
     * @param Connection $cn
     * @param string $selectChildren
     * @param string $updateMultilingualRelation
     * @param int[] $pageIDs
     */
    private function fixMultilingualPageRelations(Connection $cn, $updateMultilingualRelation, array $pageIDs)
    {
        $pageIDsJoined = implode(',', $pageIDs);
        $cn->query(str_replace(':cIDs:', $pageIDsJoined, $updateMultilingualRelation));
        $childPageIDs = $cn->query('SELECT cID FROM Pages WHERE cParentID IN (' . $pageIDsJoined . ')')->fetchAll();
        if (!empty($childPageIDs)) {
            $childPageIDs = array_map('intval', array_map('array_pop', $childPageIDs));
            foreach (array_chunk($childPageIDs, 500) as $chunk) {
                $this->fixMultilingualPageRelations($cn, $updateMultilingualRelation, $chunk);
            }
        }
    }
}
