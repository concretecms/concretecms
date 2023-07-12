<?php

namespace Concrete\Core\Announcement\Icon;

abstract class AbstractIcon implements IconInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \JsonSerializable::jsonSerialize()
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'element' => $this->getElement(),
        ];
    }
}
