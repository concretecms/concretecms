<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Controller\Element\Navigation\AccountMenu;
use Concrete\Core\Attribute\Context\DashboardFormContext;
use Concrete\Core\Attribute\Context\FrontendFormContext;
use Concrete\Core\Attribute\Form\Renderer;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Page\Desktop\DesktopList;
use Concrete\Core\Page\Theme\ThemeRouteCollection;
use Concrete\Core\User\User;
use Loader;
use Concrete\Core\Page\Controller\PageController as CorePageController;

class AccountPageController extends CorePageController implements UsesFeatureInterface
{
    public $helpers = array('html', 'form', 'text');

    public function getRequiredFeatures(): array
    {
        return [
            Features::ACCOUNT
        ];
    }

    public function on_start()
    {
        $u = $this->app->make(User::class);
        if (!$u->isRegistered()) {
            return $this->replace('/login');
        }
        $profile = \UserInfo::getByID($u->getUserID());

        $dh = \Core::make('helper/concrete/dashboard');
        $desktop = DesktopList::getMyDesktop();

        $collection = $this->app->make(ThemeRouteCollection::class);
        $theme = $collection->getThemeByRoute('/account');

        $profileFormRenderer = null;
        if ($theme === false || $theme[0] === VIEW_CORE_THEME) {
            // We're using the default theme, so let's do our fancy dashboard overriding of the theme if we can.
            if ($dh->inDashboard($desktop) && $this->getPageObject()->getCollectionPath() != '/account/welcome') {
                $this->setTheme('dashboard');
                $this->set('pageTitle', t('My Account'));
                $profileFormRenderer = new Renderer(
                    new DashboardFormContext(),
                    $profile
                );

            } else {
                $this->setTheme('concrete');
            }
        }

        if (!$profileFormRenderer) {
            $profileFormRenderer = new Renderer(
                new FrontendFormContext(),
                $profile
            );
        }

        $this->set('profileFormRenderer', $profileFormRenderer);

        $this->setThemeViewTemplate('account.php');
        $this->error = Loader::helper('validation/error');
        $this->token = Loader::helper('validation/token');
        $this->set('valt', $this->token);
        $this->set('av', Loader::helper('concrete/avatar'));

        $this->set('profile', $profile);

        $nav = new AccountMenu($this->getPageObject());
        $this->set('nav', $nav);

    }

    public function on_before_render()
    {
        $this->set('error', $this->error);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Page\Controller\PageController::useUserLocale()
     */
    public function useUserLocale()
    {
        return true;
    }
}
