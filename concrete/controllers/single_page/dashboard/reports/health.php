<?php
namespace Concrete\Controller\SinglePage\Dashboard\Reports;

use Concrete\Core\Health\Report\Result\ResultList;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Search\Pagination\PaginationFactory;

class Health extends DashboardPageController
{
    public function view()
    {
        $this->set('dateService', $this->app->make(Date::class));
        $list = new ResultList($this->entityManager);
        $list->setItemsPerPage(20);
        $pagination = $this->app->make(PaginationFactory::class)->createPaginationObject($list);
        $this->set('pagination', $pagination);
        if ($pagination->getTotalResults() > 0) {
            $this->setThemeViewTemplate('full.php');
        }
    }

}
