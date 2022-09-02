<?php
namespace Concrete\Core\Health\Report\Finding\Controls;

use Concrete\Core\Page\Page;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

abstract class DashboardPageLocation implements DashboardPageLocationInterface
{

    public function getUrl(): string
    {
        $page = Page::getByPath($this->getPagePath());
        return (string) $page->getCollectionLink();
    }

    public function jsonSerialize()
    {
        $data = [
            'class' => static::class
        ];
        return $data;
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, string $format = null, array $context = [])
    {
        // Nothing
    }

}
