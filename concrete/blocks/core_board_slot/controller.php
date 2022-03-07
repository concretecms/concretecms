<?php

namespace Concrete\Block\CoreBoardSlot;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Board\Instance\Slot\Content\ContentRenderer;
use Concrete\Core\Entity\Board\SlotTemplate;
use Doctrine\ORM\EntityManager;

class Controller extends BlockController
{
    /**
     * @var string|null
     */
    public $contentObjectCollection;

    /**
     * @var int|null
     */
    public $slotTemplateID;

    /**
     * @var int|null
     */
    public $instanceSlotID;

    /**
     * @var string
     */
    protected $btTable = 'btCoreBoardSlot';

    /**
     * @var bool
     */
    protected $btIsInternal = true;

    /**
     * @var bool
     */
    protected $btIgnorePageThemeGridFrameworkContainer = true;

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Proxy block for board slots.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Board Slot');
    }

    /**
     * @return int|null
     */
    public function getInstanceSlotID()
    {
        return $this->instanceSlotID;
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
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
