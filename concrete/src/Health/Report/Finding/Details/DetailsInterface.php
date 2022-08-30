<?php
namespace Concrete\Core\Health\Report\Finding\Details;

use Concrete\Core\Health\Report\Finding\Details\Formatter\FormatterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;

interface DetailsInterface extends \JsonSerializable, DenormalizableInterface
{

    public function getFormatter(): FormatterInterface;

}
