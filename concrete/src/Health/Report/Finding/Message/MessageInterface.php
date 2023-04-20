<?php
namespace Concrete\Core\Health\Report\Finding\Message;

use Concrete\Core\Health\Report\Finding\Message\Formatter\FormatterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;

interface MessageInterface extends \JsonSerializable, DenormalizableInterface
{

    public function getFormatter(): FormatterInterface;

}
