<?php

namespace Concrete\Core\Filesystem\Icon;

interface IconRepositoryInterface
{

    /**
     * @return IconInterface[]
     */
    public function getIcons();
}
