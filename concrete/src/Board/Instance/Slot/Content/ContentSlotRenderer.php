<?php

namespace Concrete\Core\Board\Instance\Slot\Content;

use Concrete\Core\Application\Application;

class ContentSlotRenderer
{

    /**
     * @var ObjectCollection
     */
    protected $data;

    /**
     * @var Application
     */
    protected $app;

    /**
     * ContentSlotRenderer constructor.
     * @param ObjectCollection $data
     */
    public function __construct(Application $app, ObjectCollection $data)
    {
        $this->app = $app;
        $this->data = $data;
    }


    public function display(int $slot)
    {
        $object = $this->data->getContentObjects()[$slot];
        if ($object) {
            $object->display($this->app);
        }
    }


}

