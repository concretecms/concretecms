<?php
namespace Concrete\Core\Health\Report\Export;

use Concrete\Core\Entity\Health\Report\Finding;

interface ExporterInterface
{

    /**
     * @return ColumnInterface[]
     */
    public function getColumns(): array;

    /**
     * @param ColumnInterface $column
     * @param Finding $finding
     * @return string
     */
    public function getColumnValue(ColumnInterface $column, Finding $finding): string;

}
