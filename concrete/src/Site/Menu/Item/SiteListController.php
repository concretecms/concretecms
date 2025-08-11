<?php
namespace Concrete\Core\Site\Menu\Item;


use Concrete\Core\Application\Service\Dashboard;
use Concrete\Core\Application\UserInterface\Menu\Item\Controller;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Site\Service;
use HtmlObject\Element;
use HtmlObject\Link;

class SiteListController extends Controller
{

    protected $service;
    protected $dashboard;
    protected $page;

    public function __construct(Page $page, Service $service, Dashboard $dashboard)
    {
        $this->page = $page;
        $this->service = $service;
        $this->dashboard = $dashboard;
    }

    /**
     * Determine whether item should be displayed
     *
     * @return bool
     */
    public function displayItem()
    {
        return count($this->service->getList()) > 1 && $this->page->getPageController() instanceof DashboardPageController;
    }


    public function getMenuItemLinkElement()
    {
        $ag = ResponseAssetGroup::get();
        //$element = parent::getMenuItemLinkElement();

        if ($this->displayItem() &&
            ($this->page->getPageController() instanceof DashboardSitePageController
                ||
                $this->page->getPageController() instanceof DashboardPageController && method_exists($this->page->getPageController(), 'getSite'))) {

            $request = \Request::getInstance();
            $token = \Core::make('token')->generate($request->getRequestURI());

            $element = new Element('div');
            $element->setAttribute('class', 'ccm-menu-item-site-list-container');
            $element->setAttribute('data-vue', 'cms');

            $icon = new Element('i');
            $icon->addClass('fas fa-globe');
            $element->appendChild($icon);

            $sites = [];
            foreach($this->service->getList() as $site) {
                $permissions = new \Permissions($site);
                if ($permissions->canViewSiteInSelector()) {
                    $sites[] = $site;
                }
            }
            $select = new Element('concrete-toolbar-site-list', null, [
                ':sites' => json_encode($sites),
                'token' => $token,
                'uri' => $request->getRequestURI(),
                'selected-site' => $this->service->getActiveSiteForEditing()->getSiteID(),
            ]);

            $element->appendChild($select);

            return $element;


        } else {
            $element = new Element('div');
            $element->setAttribute('class', 'ccm-menu-item-site-list-container ccm-menu-item-site-list-inactive');

            $icon = new Element('i');
            $icon->addClass('fas fa-globe');
            $element->appendChild($icon);

            $label = new Element('span');
            $label->setValue(t('Global'));
            $element->appendChild($label);
            return $element;
        }
    }

}
