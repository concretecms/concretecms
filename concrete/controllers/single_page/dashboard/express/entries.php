<?php
namespace Concrete\Controller\SinglePage\Dashboard\Express;

use Concrete\Core\Controller\Traits\DashboardExpressEntryDetailsTrait;
use Concrete\Core\Controller\Traits\DashboardSelectableExpressEntryListTrait;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Permission\Checker;

class Entries extends DashboardSitePageController
{

    use DashboardSelectableExpressEntryListTrait;
    use DashboardExpressEntryDetailsTrait;

    public function view()
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $entities = [];
        foreach($r->findPublicEntities() as $entity) {
            $permissions = new Checker($entity);
            if ($permissions->canViewExpressEntries()) {
                $entities[] = $entity;
            }
        }
        $this->set('pageTitle', t('View Express Entities'));
        $this->set('entities', $entities);
    }

}
