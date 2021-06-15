<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards\Designer;

use Concrete\Core\Board\Command\ResetBoardCustomWeightingCommand;
use Concrete\Core\Board\Designer\Command\SetCustomElementItemsCommand;
use Concrete\Core\Board\Helper\Traits\SlotTemplateJsonHelperTrait;
use Concrete\Core\Board\Instance\Item\Populator\CalendarEventPopulator;
use Concrete\Core\Board\Instance\Item\Populator\PagePopulator;
use Concrete\Core\Board\Instance\Slot\Content\ContentPopulator;
use Concrete\Core\Board\Instance\Slot\Content\ObjectCollection;
use Concrete\Core\Board\Instance\Slot\Template\AvailableTemplateCollectionFactory;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Calendar\Event\EventOccurrenceService;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\DataSource\DataSource;
use Concrete\Core\Entity\Board\Designer\CustomElement;
use Concrete\Core\Entity\Board\Designer\CustomElementItem;
use Concrete\Core\Entity\Board\Designer\ItemSelectorCustomElement;
use Concrete\Core\Entity\Board\SlotTemplate;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\Response;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Summary\Data\Collection;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Core\Validation\SanitizeService;
use Concrete\Core\View\View;

class CustomizeSlot extends DashboardSitePageController
{

    use SlotTemplateJsonHelperTrait;

    public function view($id = null)
    {
        $element = $this->getCustomElement($id);
        if (is_object($element)) {
            $availableTemplateCollectionFactory = $this->app->make(AvailableTemplateCollectionFactory::class);
            $contentPopulator = $this->app->make(ContentPopulator::class);
            $itemObjectGroups = $contentPopulator->createContentObjects($element->getItems());
            $templates = $availableTemplateCollectionFactory->getAllSlotTemplates();
            $templateOptions = $this->createSlotTemplateJsonArray($templates, $itemObjectGroups);
            $this->set('templateOptions', json_encode($templateOptions));
            $this->set('element', $element);
        } else {
            return $this->redirect('/dashboard/boards/designer');
        }
    }

    public function load_preview_window()
    {
        $view = new View('/frontend/empty');
        $view->setViewTemplate('block_preview.php');
        $view->setViewTheme(Theme::getSiteTheme());
        return new Response($view->render());
    }

    /**
     * @param $id
     * @return CustomElement
     */
    protected function getCustomElement($id)
    {
        $r = $this->entityManager->getRepository(ItemSelectorCustomElement::class);
        $element = $r->findOneById($id);
        return $element;
    }

    public function submit($elementID = null)
    {
        $element = $this->getCustomElement($elementID);
        if (is_object($element)) {
            $this->set('element', $element);
        }
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {

            $json = $this->request->request->get('selectedTemplateJson');
            $data = json_decode($json, true);

            $template = $this->entityManager->find(SlotTemplate::class, $data['template']['id']);
            $objectCollection = $data['collection'];

            $element->setContentObjectCollection($objectCollection);
            $element->setSlotTemplate($template);
            $element->setStatus(CustomElement::STATUS_READY_TO_PUBLISH);
            $this->entityManager->persist($element);
            $this->entityManager->flush();

            $this->flash('success', t('Designer element items updated.'));

            return $this->buildRedirect(['/dashboard/boards/designer/', 'view_element', $element->getID()]);
        }

        $this->view($elementID);
    }



}
