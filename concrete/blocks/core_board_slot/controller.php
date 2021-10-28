<?php
namespace Concrete\Block\CoreBoardSlot;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Board\Instance\Slot\Content\ContentRenderer;
use Concrete\Core\Entity\Board\SlotTemplate;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Doctrine\ORM\EntityManager;

class Controller extends BlockController
{
    protected $btTable = 'btCoreBoardSlot';
    protected $btIsInternal = true;
    protected $btIgnorePageThemeGridFrameworkContainer = true;

    public $contentObjectCollection;

    public $slotTemplateID;

    public $instanceSlotID;

    public function getBlockTypeDescription()
    {
        return t("Proxy block for board slots.");
    }

    public function getBlockTypeName()
    {
        return t("Board Slot");
    }

    public function getInstanceSlotID()
    {
        return $this->instanceSlotID;
    }


    public function view()
    {
        $entityManager = $this->app->make(EntityManager::class);
        $renderer = $this->app->make(ContentRenderer::class);
        $collection = $renderer->denormalizeIntoCollection(json_decode($this->contentObjectCollection, true));
        $template = $entityManager->find(SlotTemplate::class, (int) $this->slotTemplateID);
        $this->set('dataCollection', $collection);
        $this->set('renderer', $renderer);
        $this->set('template', $template);
    }

}
