<?php

namespace Concrete\Core\Application\UserInterface\Welcome\ContentItem\Action;

use Concrete\Core\Application\UserInterface\Welcome\ContentItem\AbstractAction;

class GuideAction extends AbstractAction
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
        return 'concrete-welcome-action-guide';
    }

    public function getComponentProps(): array
    {
        return [
            'guide' => $this->guide,
        ];
    }

}
