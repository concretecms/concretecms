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
            $token = \Core::make('token')->getParameter($request->getRequestURI());

            $element = new Element('div');
            $element->setAttribute('class', 'ccm-menu-item-site-list-container');

            $icon = new Element('i');
            $icon->addClass('fas fa-globe');
            $element->appendChild($icon);

            $select = new Element('select', null, [
                'class' => 'selectpicker',
                'data-live-search' => 'true',
                'data-size' => '5',
                'data-select' => 'ccm-header-site-list'
            ]);
            foreach($this->service->getList() as $site) {
                $permissions = new \Permissions($site);
                if ($permissions->canViewSiteInSelector()) {
                    if ($this->service->getActiveSiteForEditing()->getSiteID() == $site->getSiteID()) {
                        $selected = true;
                    } else {
                        $selected = false;
                    }

                    $url = \URL::to('/ccm/site/redirect', $site->getSiteID()) . '?rUri=' . urlencode($request->getRequestURI()) . '&' . $token;
                    $option = new Element('option', $site->getSiteName(), ['selected' => $selected, 'value' => $url]);
                    $select->appendChild($option);
                }
            }

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
