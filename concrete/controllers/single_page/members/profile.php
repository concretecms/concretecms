<?php
namespace Concrete\Controller\SinglePage\Members;

use Concrete\Core\Page\Controller\PublicProfilePageController;
use Loader;
use User;
use UserInfo;
use Exception;

class Profile extends PublicProfilePageController
{
    public function view($userID = 0)
    {
        $html = Loader::helper('html');
        $canEdit = false;
        $u = new User();

        if ($userID > 0) {
            $profile = UserInfo::getByID($userID);
            if (!is_object($profile)) {
                return $this->replace('/page_not_found');
            }
        } elseif ($u->isRegistered()) {
            $profile = UserInfo::getByID($u->getUserID());
        } else {
            $this->set('intro_msg', t('You must sign in order to access this page!'));
            return $this->replace('/login');
        }
        if (is_object($profile) && $profile->getUserID() == $u->getUserID()) {
            $canEdit = true;
        }

        $this->set('profile', $profile);
        $this->set('badges', $profile->getUserBadges());
        $this->set('av', Loader::helper('concrete/avatar'));
        $this->set('t', Loader::helper('text'));
        $this->set('canEdit', $canEdit);
    }
}
