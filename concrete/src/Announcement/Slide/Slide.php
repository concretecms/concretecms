<?php

namespace Concrete\Core\Announcement\Slide;

class Slide extends AbstractSlide
{

    /**
     * @var string
     */
    protected $component;

    /**
     * @var array
     */
    protected $componentProps = [];

    /**
     * Slide constructor.
     * @param string $component
     * @param array $componentProps
     */
    public function __construct(string $component, array $componentProps)
    {
        $this->component = $component;
        $this->componentProps = $componentProps;
    }

    /**
     * @return string
     */
    public function getComponent(): string
    {
        return $this->component;
    }

    /**
     * @return array
     */
    public function getComponentProps(): array
    {
        return $this->componentProps;
    }


}
