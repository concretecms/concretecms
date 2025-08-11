<?php

namespace Concrete\Core\Announcement\Action;

use Concrete\Core\Announcement\Component\AbstractComponent;

class VideoAction extends AbstractComponent implements ActionInterface
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
        return 'concrete-announcement-video-action';
    }

    public function getComponentProps(): array
    {
        return [
            'url' => $this->url,
        ];
    }

}
