<?php
namespace Concrete\Controller\SinglePage\Dashboard\Express;

use Concrete\Core\Controller\Traits\DashboardExpressEntryDetailsTrait;
use Concrete\Core\Controller\Traits\DashboardSelectableExpressEntryListTrait;
use Concrete\Core\Page\Controller\DashboardSitePageController;

class Entries extends DashboardSitePageController
{

    use DashboardSelectableExpressEntryListTrait;
    use DashboardExpressEntryDetailsTrait;

    public function view()
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $this->set('pageTitle', t('View Express Entities'));
        $this->set('entities', $r->findPublicEntities());
    }

}
