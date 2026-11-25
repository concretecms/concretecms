<?php

declare(strict_types=1);

namespace Concrete\Controller\SinglePage\Dashboard\Reports;

use Concrete\Core\Csv\Export\PageActivityExporter;
use Concrete\Core\Csv\WriterFactory;
use Concrete\Core\Form\Service\Widget\UserSelector;
use Concrete\Core\Page\Collection\Version\GlobalVersionList;
use Concrete\Core\Page\Controller\DashboardPageController;
use Symfony\Component\HttpFoundation\StreamedResponse;

defined('C5_EXECUTE') or die('Access Denied.');

class PageChanges extends DashboardPageController
{
    public function view()
    {
        $this->set('dt', $this->app->make('helper/form/date_time'));
        $this->set('userSelector', $this->app->make(UserSelector::class));
    }

    public function csv_export()
    {
        if (!$this->token->validate('export_page_changes')) {
            $this->error->add($this->token->getErrorMessage());
            $this->view();

            return;
        }

        if ($this->error->has()) {
            return $this->view();
        }

        return new StreamedResponse(
            function () {
                $writer = $this->getWriter();
                $writer->setUnloadDoctrineEveryTick(50);
                $writer->insertHeaders();
                $writer->insertList($this->getVersionList());
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=page_changes.csv',
            ]
        );
    }

    /**
     * Get the writer that will create the CSV.
     *
     * @return \Concrete\Core\Csv\Export\PageActivityExporter
     */
    private function getWriter()
    {
        return $this->app->make(
            PageActivityExporter::class,
            [
                'writer' => $this->app->make(WriterFactory::class)
                    ->createFromPath('php://output', 'w'),
            ]
        );
    }

    /**
     * Get the item list object.
     *
     * @return \Concrete\Core\Page\Collection\Version\GlobalVersionList
     */
    private function getVersionList()
    {
        /** @var \Concrete\Core\Form\Service\Widget\DateTime $dt */
        $dt = $this->app->make('helper/form/date_time');

        $startDate = $dt->translate('startDate', null, true);
        $endDate = $dt->translate('endDate', null, true);

        $versionList = new GlobalVersionList();
        $versionList->sortByDateApprovedDesc();

        if ($startDate !== null) {
            $versionList->filterByApprovedAfter($startDate);
        }

        if ($endDate !== null) {
            $versionList->filterByApprovedBefore($endDate);
        }

        $versionAuthorID = (int) $this->request->request->get('versionAuthorID', 0);
        if ($versionAuthorID > 0) {
            $versionList->filterByVersionAuthorUserID($versionAuthorID);
        }

        return $versionList;
    }
}
