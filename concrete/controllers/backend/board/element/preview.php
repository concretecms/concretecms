<?php
namespace Concrete\Controller\Backend\Board\Element;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Board\Instance\Slot\Content\ContentRenderer;
use Concrete\Core\Board\Instance\Slot\Menu\Manager;
use Concrete\Core\Board\Instance\Slot\RenderedSlot;
use Concrete\Core\Board\Instance\Slot\SlotRenderer;
use Concrete\Core\Controller\Controller;
use Concrete\Core\Entity\Board\Designer\CustomElement;
use Concrete\Core\Entity\Board\Designer\ItemSelectorCustomElement;
use Concrete\Core\Entity\Board\InstanceSlotRule;
use Concrete\Core\Entity\Board\SlotTemplate;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Permission\Checker;
use Concrete\Core\View\View;
use Doctrine\ORM\EntityManager;

class Preview extends Controller
{

    protected $viewPath = '/backend/board/element/preview';

    public function __construct()
    {
        parent::__construct();

        $this->view = new View($this->viewPath);
        $this->view->setViewTemplate('block_preview.php');
    }

    public function view($elementID)
    {
        $page = Page::getByPath('/dashboard/boards/designer');
        $permissions = new Checker($page);
        $element = null;
        if ($permissions->canViewPage()) {
            $entityManager = $this->app->make(EntityManager::class);
            $element = $entityManager->find(CustomElement::class, $elementID);
        }

        if ($element) {

            // @TODO this needs to be updated to be modular; right now it
            // assumes this is an ItemSelectorCustomElement
            /**
             * @var $element ItemSelectorCustomElement
             */
            $renderer = $this->app->make(ContentRenderer::class);
            $collection = $renderer->denormalizeIntoCollection($element->getContentObjectCollection());
            $template = $element->getSlotTemplate();
            $this->set('dataCollection', $collection);
            $this->set('renderer', $renderer);
            $this->set('template', $template);

            $theme = Theme::getSiteTheme();
            $this->setTheme($theme);
        } else {
            throw new \RuntimeException('Access Denied.');
        }
    }




}
