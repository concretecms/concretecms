<?php
namespace Concrete\Block\DashboardSiteActivity;

use Loader;
use Core;
use \Concrete\Core\Block\BlockController;
use User;
use UserInfo;
use \Concrete\Core\User\Statistics as UserStatistics;
use \Concrete\Block\Form\Statistics as FormBlockStatistics;
use \Concrete\Core\Page\Statistics as PageStatistics;

class Controller extends BlockController
{
    protected $btCacheBlockRecord = true;
    protected $btIsInternal = true;

    public function getBlockTypeDescription()
    {
        return t("Displays a summary of website activity.");
    }

    public function getBlockTypeName()
    {
        return t("Dashboard Site Activity");
    }

    public function view()
    {
        $u = new User();
        $ui = UserInfo::getByID($u->getUserID());
        $dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
        if (is_object($ui)) {
            $this->set('uLastLogin', $dh->formatDateTime($ui->getLastLogin()));
            $this->set('uName', $ui->getUserName());
            $this->set('lastEditSite', $dh->formatDateTime(PageStatistics::getSiteLastEdit()));
            $llu = UserStatistics::getLastLoggedInUser();
            if ($llu->getUserID() == $u->getUserID()) {
                $this->set('lastLoginSite', t('Your login is the most recent.'));
            } else {
                $this->set('lastLoginSite', $dh->formatDateTime($llu->getLastLogin()));
            }
        }
        $this->set('totalFormSubmissions', FormBlockStatistics::getTotalSubmissions());
        $now = new \DateTime('now', $dh->getTimezone('user'));
        $this->set('totalFormSubmissionsToday', FormBlockStatistics::getTotalSubmissions($now->format('Y-m-d')));
    }

}
