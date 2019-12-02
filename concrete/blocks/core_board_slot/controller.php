<?php
namespace Concrete\Block\CoreBoardSlot;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Board\Instance\Slot\Content\ContentRenderer;
use Concrete\Core\Entity\Board\SlotTemplate;
use Doctrine\ORM\EntityManager;

class Controller extends BlockController
{
    protected $btTable = 'btCoreBoardSlot';
    protected $btIsInternal = true;
    protected $btIgnorePageThemeGridFrameworkContainer = true;
    
    public $contentObjectCollection;
    
    public $slotTemplateID;
    
    public function getBlockTypeDescription()
    {
        return t("Proxy block for board slots.");
    }

    public function getBlockTypeName()
    {
        return t("Board Slot");
    }
    
    public function view()
    {
        $template = null;
        if ($this->slotTemplateID) {
            $template = $this->app->make(EntityManager::class)
                ->find(SlotTemplate::class, $this->slotTemplateID);
            $renderer = $this->app->make(ContentRenderer::class);
            $collection = $renderer->denormalizeIntoCollection(json_decode($this->contentObjectCollection, true));
            $this->set('dataCollection', $collection);
            $this->set('renderer', $renderer);
            $this->set('template', $template);
        }
    }
    
}
