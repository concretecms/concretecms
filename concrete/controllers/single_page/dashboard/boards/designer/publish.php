<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards\Designer;

use Concrete\Core\Board\Command\ResetBoardCustomWeightingCommand;
use Concrete\Core\Board\Designer\Command\ScheduleCustomElementCommand;
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
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\SlotTemplate;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Summary\Data\Collection;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Core\Validation\SanitizeService;
use Symfony\Component\HttpFoundation\JsonResponse;

class Publish extends DashboardSitePageController
{

    public function view($id = null)
    {
        $element = $this->getCustomElement($id);
        if (is_object($element)) {
            $instances = [];
            foreach($this->entityManager->getRepository(Instance::class)->findBy([], ['boardInstanceName' => 'asc']) as $instance) {
                $permissions = new Checker($instance->getBoard());
                if ($permissions->canEditBoardContents()) { // @TODO - make this is a separate permission?
                    $instances[] = $instance;
                }
            }
            $this->set('date', new Date());
            $this->set('instances', $instances);
            $this->set('element', $element);
        } else {
            return $this->redirect('/dashboard/boards/designer');
        }
    }

    /**
     * @param $id
     * @return CustomElement
     */
    protected function getCustomElement($id)
    {
        $r = $this->entityManager->getRepository(CustomElement::class);
        $element = $r->findOneById($id);
        return $element;
    }

    public function submit()
    {
        $element = $this->getCustomElement($this->request->request->get('elementId'));
        if (is_object($element)) {
            $this->set('element', $element);
        }
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $command = new ScheduleCustomElementCommand($element);
            $instances = [];
            foreach((array) $this->request->request->get('instances') as $instanceId) {
                $instance = $this->entityManager->find(Instance::class, $instanceId);
                if ($instance) {
                    $board = $instance->getBoard();
                    $checker = new Checker($board);
                    if ($checker->canEditBoardSettings()) {
                        $instances[] = $instance;
                    }
                }
            }
            $command->setInstances($instances);
            $command->setLockType($this->request->request->get('lockType'));
            $command->setTimezone($this->request->request->get('timezone'));
            $command->setStartDate($this->request->request->get('start'));
            $command->setEndDate($this->request->request->get('end'));
            $this->app->executeCommand($command);

            $this->flash('success', t('Board element scheduled successfully.'));
            return new JsonResponse([]);
        }

    }



}
