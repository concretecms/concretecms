<?php

namespace Concrete\Core\Health\Report\Result\Formatter;

use Concrete\Core\Entity\Health\Report\Result;
use Concrete\Core\Health\Report\Export\Column;
use Concrete\Core\Health\Report\Export\ExporterInterface;
use Concrete\Core\Health\Report\Export\StandardExporter;

class StandardFormatter implements FormatterInterface
{

    public function getFindingsHeading(Result $result): string
    {
        return t('Full Findings List');
    }

    public function getExporter(): ExporterInterface
    {
        return new StandardExporter();
    }


}