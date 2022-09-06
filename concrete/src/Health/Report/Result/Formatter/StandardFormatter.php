<?php

namespace Concrete\Core\Health\Report\Result\Formatter;

use Concrete\Core\Entity\Health\Report\Result;

class StandardFormatter implements FormatterInterface
{

    public function getFindingsHeading(Result $result): string
    {
        return t('Full Findings List');
    }


}