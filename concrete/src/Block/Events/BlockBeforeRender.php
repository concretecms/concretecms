<?php

namespace Concrete\Core\Block\Events;

/**
 * @since 8.4.0
 */
class BlockBeforeRender extends BlockEvent
{
    /**
     * @return \Concrete\Core\Block\Block
     */
    public function getBlock()
    {
        return $this->getSubject();
    }

    public function preventRendering()
    {
        $this->setArgument('render', false);
    }

    /**
     * Return true if the block should be rendered.
     *
     * @return bool
     */
    public function proceed()
    {
        // By default the block is rendered
        if (!$this->hasArgument('render')) {
            return true;
        }

        return $this->getArgument('render');
    }
}
