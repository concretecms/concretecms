<?php

namespace Concrete\Core\Health\Report\Result\Formatter;

use Concrete\Core\Entity\Health\Report\Result;
use Concrete\Core\Health\Report\Export\ColumnInterface;
use Concrete\Core\Health\Report\Export\ExporterInterface;

interface FormatterInterface
{

    /**
     * @param Result $result
     * @return string
     */
    public function getFindingsHeading(Result $result): string;

    /**
     * @return ExporterInterface
     */
    public function getExporter(): ExporterInterface;

}