<?php
namespace Concrete\Core\Page\Summary\Template;

use Concrete\Core\Entity\Page\Summary\PageTemplate;
use Concrete\Core\Page\Page;
use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\Template\AbstractPopulator;

class Populator extends AbstractPopulator
{

    /**
     * @param Page $mixed
     */
    public function clearAvailableTemplates(CategoryMemberInterface $mixed)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->delete(PageTemplate::class, 'pt')
            ->where('pt.cID = :cID');
        $queryBuilder->setParameter('cID', $mixed->getCollectionID());
        $queryBuilder->getQuery()->execute();
    }

    /**
     * @param Page $mixed
     */
    public function createCategoryTemplate(CategoryMemberInterface $mixed)
    {
        $pageTemplate = new PageTemplate();
        $pageTemplate->setPageID($mixed->getCollectionID());
        return $pageTemplate;
    }

}
