<?php
namespace Concrete\Core\Health\Report\Finding\Control;

use Concrete\Core\Health\Report\Finding\Control\Formatter\FormatterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;

interface ControlInterface extends \JsonSerializable, DenormalizableInterface
{

    public function getFormatter(): FormatterInterface;

}
