<?php

namespace Concrete\Core\Health\Report\Result\Formatter;

use Concrete\Core\Entity\Health\Report\Result;

interface FormatterInterface
{

    public function getFindingsHeading(Result $result): string;

}