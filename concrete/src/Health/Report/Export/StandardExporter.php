<?php
namespace Concrete\Core\Health\Report\Export;


use Concrete\Core\Entity\Health\Report\Finding;

class StandardExporter implements ExporterInterface
{

    public function getColumns(): array
    {
        return [
            new Column('type', t('Type')),
            new Column('message', t('Message')),
        ];
    }

    public function getColumnValue(ColumnInterface $column, Finding $finding): string
    {
        if ($column->getKey() === 'type') {
            return $finding->getFormatter()->getType();
        }
        if ($column->getKey() === 'message') {
            return $finding->getMessage()->getMessage();
        }
    }
}
