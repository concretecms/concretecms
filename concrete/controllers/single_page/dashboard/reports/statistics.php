<?php
namespace Concrete\Controller\SinglePage\Dashboard\Reports;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Statistics as PageStatistics;
use Loader;
use File;
use User;
use Concrete\Core\User\Statistics as UserStatistics;

class Statistics extends DashboardPageController
{
    protected $labels = array();
    protected $dates = array();

    protected function setLatestPageViews()
    {
        $viewsArray = array();
        $u = new User();
        foreach ($this->dates as $i => $date) {
            $total = PageStatistics::getTotalPageViewsForOthers($u, $date);
            $viewsArray[$this->labels[$i]] = $total;
        }
        $this->set('pageViews', $viewsArray);
    }

    protected function setLatestPagesCreated()
    {
        $viewsArray = array();
        $u = new User();
        foreach ($this->dates as $i => $date) {
            $total = PageStatistics::getTotalPagesCreated($date);
            $newPages[$this->labels[$i]] = $total;
        }
        $this->set('newPages', $newPages);
    }

    protected function setLatestRegistrations()
    {
        $registrationsArray = array();
        foreach ($this->dates as $i => $date) {
            $total = UserStatistics::getTotalRegistrationsForDay($date);
            $registrationsArray[$this->labels[$i]] = $total;
        }
        $this->set('userRegistrations', $registrationsArray);
    }

    public function on_start()
    {
        $dh = Loader::helper('date');
        for ($i = -4; $i < 1; ++$i) {
            $date = date('Y-m-d', strtotime($i . ' days'));
            if ($i == 0) {
                $label = t('Today');
            } else {
                $label = $dh->date('D', strtotime($i . ' days'));
            }
            $this->labels[] = $label;
            $this->dates[] = $date;
        }
        parent::on_start();
    }

    protected function setDownloadStatistics()
    {
        $downloads = File::getDownloadStatistics(5);
        $this->set('downloads', $downloads);
    }

    public function view()
    {
        $this->addFooterItem(Loader::helper('html')->javascript('jquery-visualize.js'));
        $this->addFooterItem(Loader::helper('html')->css('jquery-visualize.css'));
        $this->setLatestPageViews();
        $this->setLatestPagesCreated();
        $this->setLatestRegistrations();
        $this->setDownloadStatistics();

        $this->set('totalVersions', PageStatistics::getTotalPageVersions());
        $this->set('totalEditMode', PageStatistics::getTotalPagesCheckedOut());
    }
}
