<?php
namespace Concrete\Core\Health\Report\Finding\Control;

use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;

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
