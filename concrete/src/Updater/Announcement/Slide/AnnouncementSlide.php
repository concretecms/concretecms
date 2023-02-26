<?php

namespace Concrete\Core\Updater\Announcement\Slide;

use Concrete\Core\Application\UserInterface\Welcome\ContentItem\ActionInterface;
use Concrete\Core\Application\UserInterface\Welcome\ContentItem\ItemInterface;
use Concrete\Core\Application\UserInterface\Welcome\Modal\Slide\AbstractSlide;

class AnnouncementSlide extends AbstractSlide
{

    /**
     * @var string
     */
    protected $title;

    /**
     * @var ItemInterface[]
     */
    protected $items = [];

    /**
     * @var ActionInterface|null
     */
    protected $button;

    public function __construct($title, array $items = [], ActionInterface $button = null)
    {
        $this->title = $title;
        $this->items = $items;
        $this->button = $button;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return ItemInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getComponent(): string
    {
        return 'concrete-welcome-content-update-announcement';
    }

    /**
     * @return ActionInterface|null
     */
    public function getButton(): ?ActionInterface
    {
        return $this->button;
    }

    public function getComponentProps(): array
    {
        return [
            'title' => $this->getTitle(),
            'items' => $this->getItems(),
            'button' => $this->getButton(),
        ];
    }




}
