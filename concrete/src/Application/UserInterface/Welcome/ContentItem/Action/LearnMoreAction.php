<?php

namespace Concrete\Core\Application\UserInterface\Welcome\ContentItem\Action;

use Concrete\Core\Application\UserInterface\Welcome\ContentItem\AbstractAction;

class LearnMoreAction extends AbstractAction
{

    /**
     * @var string
     */
    protected $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getComponent(): string
    {
        return 'concrete-welcome-action-external-link';
    }

    public function getComponentProps(): array
    {
        return [
            'url' => $this->url,
        ];
    }

}
