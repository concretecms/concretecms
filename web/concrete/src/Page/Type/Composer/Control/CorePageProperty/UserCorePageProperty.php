<?php
namespace Concrete\Core\Page\Type\Composer\Control\CorePageProperty;

use Loader;
use UserInfo;
use Concrete\Core\Page\Page;

class UserCorePageProperty extends CorePageProperty
{
    public function __construct()
    {
        $this->setCorePagePropertyHandle('user');
        $this->setPageTypeComposerControlName(tc('PageTypeComposerControlName', 'User'));
        $this->setPageTypeComposerControlIconSRC(ASSETS_URL . '/attributes/text/icon.png');
    }

    public function publishToPage(Page $c, $data, $controls)
    {
        if (Loader::helper('validation/numbers')->integer($data['user'])) {
            $this->addPageTypeComposerControlRequestValue('uID', $data['user']);
        }
        parent::publishToPage($c, $data, $controls);
    }

    public function validate()
    {
        $uID = $this->getPageTypeComposerControlDraftValue();
        $ux = UserInfo::getByID($uID);
        $e = Loader::helper('validation/error');
        if (!is_object($ux)) {
            $e->add(t('You must specify a valid user.'));

            return $e;
        }
    }

    public function getPageTypeComposerControlDraftValue()
    {
        if (is_object($this->page)) {
            $c = $this->page;

            return $c->getCollectionUserID();
        }
    }

}
