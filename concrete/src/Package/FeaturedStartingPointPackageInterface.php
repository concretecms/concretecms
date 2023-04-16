<?php
namespace Concrete\Core\Package;

interface FeaturedStartingPointPackageInterface
{

    /**
     * @return string
     */
    public function getStartingPointThumbnail(): string;

    /**
     * @return string[]
     */
    public function getStartingPointDescriptionLines(): array;

}
