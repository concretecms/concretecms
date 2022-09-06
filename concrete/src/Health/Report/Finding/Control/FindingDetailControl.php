<?php
namespace Concrete\Core\Health\Report\Finding\Control;

use Concrete\Core\Health\Report\Finding\Control\Formatter\FindingDetailFormatter;
use Concrete\Core\Health\Report\Finding\Control\Formatter\FormatterInterface;
use Concrete\Core\Health\Report\Finding\Control\Traits\SimpleSerializableAndDenormalizableClassTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Opens a modal window to show the details about a particular Finding.
 * Since the finding is available from the parent context we don't need to pass it in here.
 */
class FindingDetailControl implements ControlInterface
{

    use SimpleSerializableAndDenormalizableClassTrait;

    public function getFormatter(): FormatterInterface
    {
        return new FindingDetailFormatter();
    }

}
