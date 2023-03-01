<?php

namespace Concrete\Core\Announcement\Action;

use Concrete\Core\Announcement\Component\AbstractComponent;

class GuideAction extends AbstractComponent implements ActionInterface
{

    /**
     * @var string
     */
    protected $guide;

    public function __construct(string $guide)
    {
        $this->guide = $guide;
    }

    public function getComponent(): string
    {
        return 'concrete-announcement-guide-action';
    }

    public function getComponentProps(): array
    {
        return [
            'guide' => $this->guide,
        ];
    }

}
