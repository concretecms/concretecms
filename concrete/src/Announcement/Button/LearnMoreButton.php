<?php

namespace Concrete\Core\Announcement\Button;

use Concrete\Core\Announcement\Button\ButtonInterface;
use Concrete\Core\Announcement\Component\AbstractComponent;

class LearnMoreButton extends AbstractComponent implements ButtonInterface
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
        return 'concrete-announcement-external-link-button';
    }

    public function getComponentProps(): array
    {
        return [
            'url' => $this->url,
            'title' => $this->title,
        ];
    }

}
