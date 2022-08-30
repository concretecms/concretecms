<?php
namespace Concrete\Core\Health\Report\Finding\Details;

use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;

interface LocationInterface extends \JsonSerializable, DenormalizableInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getUrl(): string;


}
