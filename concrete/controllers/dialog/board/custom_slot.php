<?php
namespace Concrete\Controller\Dialog\Board;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Board\Command\AddCustomBlockToBoardCommand;
use Concrete\Core\Board\Instance\Slot\Content\ContentPopulator;
use Concrete\Core\Board\Instance\Slot\Content\ContentRenderer;
use Concrete\Core\Board\Instance\Slot\Content\ObjectCollection;
use Concrete\Core\Board\Instance\Slot\Template\AvailableTemplateCollectionFactory;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceItem;
use Concrete\Core\Entity\Board\InstanceItemRepository;
use Concrete\Core\Entity\Board\InstanceSlotRule;
use Concrete\Core\Entity\Board\SlotTemplate;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Permission\Checker;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;

class CustomSlot extends \Concrete\Core\Controller\Controller
{

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
        $renderer = $this->app->make(ContentRenderer::class);

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
        $contentObjects = [];
        foreach($itemObjectGroups as $itemObjectGroup) {
            $contentObjects = array_merge($itemObjectGroup->getContentObjects(), $contentObjects);
        }

        $options = [];
        foreach($templates as $template) {
            foreach($contentObjects as $contentObject) {
                $objectCollection = new ObjectCollection();
                $objectCollection->addContentObject(1, $contentObject);
                $options[] = [
                    'template' => $template,
                    'collection' => $objectCollection,
                    'content' => $renderer->render($objectCollection, $template)
                ];
            }
        }

        return new JsonResponse($options);
    }

    public function replace()
    {
        $instance = $this->getInstanceFromRequest();
        $dataSources = $instance->getBoard()->getDataSources()->toArray();
        $this->set('dataSourcesJson', json_encode($dataSources));
        $this->set('instance', $instance);
        $this->set('slot', h($this->request->query->get('slot')));
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

        $command = new AddCustomBlockToBoardCommand();
        $command->setBlockID($block->getBlockID());
        $command->setSlot($slot);
        $command->setInstance($instance);
        $this->app->executeCommand($command);

        return new JsonResponse([]);

    }

}
