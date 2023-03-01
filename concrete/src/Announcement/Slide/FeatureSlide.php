<?php

namespace Concrete\Core\Announcement\Slide;

use Concrete\Core\Announcement\Action\ActionInterface;
use Concrete\Core\Announcement\Button\ButtonInterface;
use Concrete\Core\Announcement\Item\ItemInterface;

class FeatureSlide extends AbstractSlide
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
     * @var ButtonInterface|null
     */
    protected $button;

    public function __construct($title, array $items = [], ButtonInterface $button = null)
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
        return 'concrete-announcement-feature-slide';
    }

    /**
     * @return ButtonInterface|null
     */
    public function getButton(): ?ButtonInterface
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
