<?php
namespace Concrete\Core\File\Component\Chooser;

interface FilterCollectionInterface extends \JsonSerializable
{

    /**
     * @return FilterInterface[]
     */
    public function getFilters(): array;
    
}