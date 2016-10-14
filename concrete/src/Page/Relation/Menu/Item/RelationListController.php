<?php

namespace Concrete\Core\Page\Relation\Menu\Item;


use Concrete\Controller\Panel\PageRelations;
use Concrete\Core\Application\Service\Dashboard;
use Concrete\Core\Application\UserInterface\Menu\Item\Controller;
use Concrete\Core\Multilingual\Service\UserInterface\Flag;
use Concrete\Core\Page\Page;
use HtmlObject\Element;
use HtmlObject\Link;

class RelationListController extends Controller
{

    protected $dashboard;
    protected $page;
    protected $flagService;
    protected $multilingualSection;

    public function __construct(Page $page, Dashboard $dashboard, Flag $flagService)
    {
        $this->page = $page;
        $this->dashboard = $dashboard;
        $this->flagService = $flagService;
        $this->multilingualSection = \Concrete\Core\Multilingual\Page\Section\Section::getBySectionOfSite($page);
    }

    /**
     * Determine whether item should be displayed
     *
     * @return bool
     */
    public function displayItem()
    {

        $controller = new PageRelations();
        $controller->setPageObject($this->page);
        return $controller->canAccessPanel();
    }


    public function getMenuItemLinkElement()
    {

        $link = new Link('#', '');
        $link->setAttribute('data-panel-url', \URL::to('/ccm/system/panels/page/relations'));
        $link->setAttribute('title', t('Navigate this page in other languages'));
        $link->setAttribute('data-launch-panel', 'page_relations');

        $icon = $this->flagService->getFlagIcon($this->multilingualSection->getIcon());

        $accessibility = new Element('span', $this->multilingualSection->getLanguageText());
        $accessibility->addClass('ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-add-page');

        $link->appendChild($icon);
        $link->appendChild($accessibility);

        return $link;
    }

}