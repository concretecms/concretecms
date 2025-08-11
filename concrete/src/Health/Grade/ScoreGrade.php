<?php
namespace Concrete\Core\Health\Grade;

use Concrete\Core\Health\Grade\Formatter\FormatterInterface;
use Concrete\Core\Health\Grade\Formatter\ScoreFormatter;

class ScoreGrade extends AbstractGrade
{

    public function getFormatter(): FormatterInterface
    {
        return new ScoreFormatter($this);
    }



}
