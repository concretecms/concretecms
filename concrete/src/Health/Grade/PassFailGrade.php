<?php
namespace Concrete\Core\Health\Grade;

use Concrete\Core\Health\Grade\Formatter\FormatterInterface;
use Concrete\Core\Health\Grade\Formatter\PassFailFormatter;

class PassFailGrade extends AbstractGrade
{

    public function getFormatter(): FormatterInterface
    {
        return new PassFailFormatter($this);
    }

    public function hasPassed(): bool
    {
        return $this->getScore() > 70;
    }
}
