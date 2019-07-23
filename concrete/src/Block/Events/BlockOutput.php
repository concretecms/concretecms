<?php

namespace Concrete\Core\Block\Events;

class BlockOutput extends BlockEvent
{
    /**
     * @return \Concrete\Core\Block\Block
     */
    public function getBlock()
    {
        return $this->getSubject();
    }

    /**
     * @param string $contents
     */
    public function setContents($contents)
    {
        $this->setArgument('contents', $contents);
    }

    /**
     * @return string
     */
    public function getContents()
    {
        return $this->getArgument('contents');
    }
}
