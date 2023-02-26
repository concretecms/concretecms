<?php

namespace Concrete\Core\Application\UserInterface\Welcome\ContentItem\Action\Button;

use Concrete\Core\Application\UserInterface\Welcome\ContentItem\AbstractAction;

class LearnMoreButton extends AbstractAction
{

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $title;

    public function __construct(string $url, string $title)
    {
        $this->url = $url;
        $this->title = $title;
    }

    public function getComponent(): string
    {
        return 'concrete-welcome-button-external-link';
    }

    public function getComponentProps(): array
    {
        return [
            'url' => $this->url,
            'title' => $this->title,
        ];
    }

}
