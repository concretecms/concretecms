<?php
namespace Concrete\Core\Health\Grade;

use Concrete\Core\Health\Grade\Formatter\FormatterInterface;

interface GradeInterface extends \JsonSerializable
{

    public function getFormatter(): FormatterInterface;

}
