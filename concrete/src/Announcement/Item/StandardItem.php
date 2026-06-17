<?php

namespace Concrete\Core\Announcement\Item;

use Concrete\Core\Announcement\Icon\IconInterface;

class StandardItem implements ItemInterface
{

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var IconInterface|null
     */
    protected $icon;

    /**
     * @var array
     */
    protected $actions = [];

    public function __construct(string $title, string $description, array $actions = [], ?IconInterface $icon = null)
    {
        $this->title = $title;
        $this->description = $description;
        $this->actions = $actions;
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return IconInterface|null
     */
    public function getIcon(): ?IconInterface
    {
        return $this->icon;
    }

    /**
     * @param IconInterface|null $icon
     */
    public function setIcon(?IconInterface $icon): void
    {
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return array
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param array $actions
     */
    public function setActions(array $actions): void
    {
        $this->actions = $actions;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'title' => $this->getTitle(),
            'icon' => $this->getIcon(),
            'description' => $this->getDescription(),
            'actions' => $this->getActions(),
        ];
    }


}
