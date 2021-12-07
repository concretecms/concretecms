<?php
namespace Concrete\Core\File\Component\Chooser;

interface ChooserConfigurationInterface
{

    /**
     * @return UploaderOptionInterface[]
     */
    public function getUploaders(): array;

    /**
     * @return ChooserOptionInterface[]
     */
    public function getChoosers(): array;

    /**
     * @return FilterCollectionInterface|null
     */
    public function getFilters(): ?FilterCollectionInterface;


}