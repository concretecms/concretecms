<?php
namespace Concrete\Core\Health\Report\Finding\Controls;

use Concrete\Core\Health\Report\Finding\Controls\Formatter\FormatterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;

interface ControlsInterface extends \JsonSerializable, DenormalizableInterface
{

    public function getFormatter(): FormatterInterface;

}
