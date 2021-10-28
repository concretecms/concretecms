<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Config;
use Loader;
use Localization;

class Multilingual extends DashboardPageController
{
    public $helpers = array('form');

    public function view()
    {
        $locales = Localization::getAvailableInterfaceLanguageDescriptions();
        $this->set('LANGUAGE_CHOOSE_ON_LOGIN', Config::get('concrete.i18n.choose_language_login'));
        $this->set('interfacelocales', $locales);
        $this->set('SITE_LOCALE', Config::get('concrete.locale'));
        $mlLink = null;
        $mlPage = Page::getByPath('/dashboard/system/multilingual/setup');
        if ($mlPage && !$mlPage->isError()) {
            $cp = new Checker($mlPage);
            if ($cp->canViewPage()) {
                $mlLink = [
                    t($mlPage->getCollectionName()),
                    $mlPage->getCollectionLink(),
                ];
            }
        }
        $this->set('mlLink', $mlLink);
    }

    public function on_start()
    {
        parent::on_start();
        $this->token = Loader::helper('validation/token');
    }

    public function interface_settings_saved()
    {
        $this->set('success', t('Interface settings saved. Please log out and in again to update all backend messages.'));
        $this->view();
    }
    public function save_interface_language()
    {
        if (Loader::helper('validation/token')->validate('save_interface_language')) {
            if ($this->post('SITE_LOCALE')) {
                Config::save('concrete.locale', $this->post('SITE_LOCALE'));
            }
            Config::save('concrete.i18n.choose_language_login', $this->post('LANGUAGE_CHOOSE_ON_LOGIN'));
            $this->redirect('/dashboard/system/basics/multilingual', 'interface_settings_saved');
        } else {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        $this->view();
    }
}
