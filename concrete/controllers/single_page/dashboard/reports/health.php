<?php

namespace Concrete\Controller\SinglePage\Dashboard\Reports;

use Concrete\Core\Health\Report\Result\ResultList;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\Pagination\PaginationFactory;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

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
        $newReportPage = Page::getByPath('/dashboard/welcome/health');
        if ($newReportPage && !$newReportPage->isError() && (new Checker($newReportPage))->canRead()) {
            $newReportPageUrl = (string) $this->app->make(ResolverManagerInterface::class)->resolve([$newReportPage]);
        } else {
            $newReportPageUrl = '';
        }
        $this->set('newReportPageUrl', $newReportPageUrl);
    }
}
