<?php

namespace Concrete\Core\File\Component\Chooser;

class ChooserConfiguration implements ChooserConfigurationInterface
{

    /**
     * @var FilterCollectionInterface|null
     */
    protected $filters;

    /**
     * @var UploaderOptionInterface[]
     */
    protected $uploaders = [];

    /**
     * @var ChooserOptionInterface[]
     */
    protected $choosers = [];

    public function addChooser(ChooserOptionInterface $chooserOption)
    {
        $this->choosers[] = $chooserOption;
    }

    public function addUploader(UploaderOptionInterface $uploaderOption)
    {
        $this->uploaders[] = $uploaderOption;
    }

    /**
     * @return UploaderOptionInterface[]
     */
    public function getUploaders(): array
    {
        return $this->uploaders;
    }

    /**
     * @return ChooserOptionInterface[]
     */
    public function getChoosers(): array
    {
        return $this->choosers;
    }

    /**
     * @return FilterCollectionInterface|null
     */
    public function getFilters(): ?FilterCollectionInterface
    {
        return $this->filters;
    }

    /**
     * @param FilterCollectionInterface|null $filters
     */
    public function setFilters(FilterCollectionInterface $filters): void
    {
        $this->filters = $filters;
    }


}