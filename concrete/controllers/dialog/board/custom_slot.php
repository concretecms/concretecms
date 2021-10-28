<?php
namespace Concrete\Controller\Dialog\Board;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Board\Command\AddCustomSlotToBoardCommand;
use Concrete\Core\Board\Helper\Traits\SlotTemplateJsonHelperTrait;
use Concrete\Core\Board\Instance\Slot\Content\AvailableObjectCollectionFactory;
use Concrete\Core\Board\Instance\Slot\Content\ContentPopulator;
use Concrete\Core\Board\Instance\Slot\Content\ContentRenderer;
use Concrete\Core\Board\Instance\Slot\Content\ObjectCollection;
use Concrete\Core\Board\Instance\Slot\Template\AvailableTemplateCollectionFactory;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceItem;
use Concrete\Core\Entity\Board\SlotTemplate;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Permission\Checker;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CustomSlot extends \Concrete\Core\Controller\Controller
{

    use SlotTemplateJsonHelperTrait;

    protected $viewPath = '/dialogs/boards/custom_slot';


    protected function getInstanceFromRequest()
    {
        $entityManager = $this->app->make(EntityManager::class);
        $canEdit = false;
        if ($this->request->query->has('boardInstanceID')) {
            /**
             * @var $instance Instance
             */
            $instance = $entityManager->find(Instance::class, $this->request->query->get('boardInstanceID'));
            if ($instance) {
                $permissions = new Checker($instance->getBoard());
                if ($permissions->canEditBoardContents()) {
                    $canEdit = true;
                }
            }
        }
        if (!$canEdit) {
            throw new UserMessageException(t('Access Denied'));
        } else {
            return $instance;
        }
    }

    public function getTemplates()
    {
        $entityManager = $this->app->make(EntityManager::class);
        $contentPopulator = $this->app->make(ContentPopulator::class);
        $availableTemplateCollectionFactory = $this->app->make(AvailableTemplateCollectionFactory::class);

        $instance = $this->getInstanceFromRequest();
        $items = [];
        if (!empty($this->request->request->get('selectedItemIds'))) {
            foreach ($this->request->request->get('selectedItemIds') as $itemId) {
                $items[] = $entityManager->find(InstanceItem::class, $itemId);
            }
        }
        $templates = $availableTemplateCollectionFactory->getAvailableTemplates(
            $instance, $this->request->request->get('slot')
        );

        $itemObjectGroups = $contentPopulator->createContentObjects($items);
        return new JsonResponse($this->createSlotTemplateJsonArray($templates, $itemObjectGroups));
    }

    public function replace()
    {
        $instance = $this->getInstanceFromRequest();
        $this->set('dataSourcesJson', json_encode($this->getDataSourcesJson($instance)));
        $this->set('instance', $instance);
        $this->set('slot', h($this->request->query->get('slot')));
    }

    public function searchItems()
    {
        $instance = $this->getInstanceFromRequest();
        return new JsonResponse($this->getDataSourcesJson(
            $instance, $this->request->request->get('keywords')
        ));
    }

    protected function getDataSourcesJson(Instance $instance, $keywords = null)
    {
        $dataSources = [];
        $entityManager = $this->app->make(EntityManager::class);
        foreach($instance->getBoard()->getDataSources() as $dataSource) {
            $items = $entityManager->getRepository(InstanceItem::class)
                ->findByDataSource($dataSource, $instance, $keywords);
            $dataSources[] = [
                'id' => $dataSource->getConfiguredDataSourceID(),
                'name' => $dataSource->getName(),
                'items' => $items,
            ];
        }
        return $dataSources;
    }

    public function saveTemplate()
    {
        $instance = $this->getInstanceFromRequest();
        $slot = $this->request->request->get('slot');
        $entityManager = $this->app->make(EntityManager::class);
        $serializer = $this->app->make(JsonSerializer::class);

        $data = $this->request->request->get('selectedTemplateOption');
        $template = $entityManager->find(SlotTemplate::class, $data['template']['id']);
        $collection = $serializer->serialize($data['collection'], 'json');

        $type = BlockType::getByHandle(BLOCK_HANDLE_BOARD_SLOT_PROXY);
        $data = [
            'contentObjectCollection' => $collection,
            'slotTemplateID' => $template->getId(),
        ];
        $block = $type->add($data);

        $command = new AddCustomSlotToBoardCommand();
        $command->setBlockID($block->getBlockID());
        $command->setSlot($slot);
        $command->setInstance($instance);
        $rule = $this->app->executeCommand($command);
        return new JsonResponse($rule);








        \Cache::disableAll(); // This is required to make block output rendering work. This is not ideal.
        $block = Block::getByID($block->getBlockID()); // need to make sure everything is refreshed.
        $view = new BlockView($block);
        ob_start();
        $view->render('view');
        $content = ob_get_contents();
        ob_end_clean();
        
        return new Response($content);
    }

}
