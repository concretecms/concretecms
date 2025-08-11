<?php
namespace Concrete\Core\Health\Report\Export;

use Concrete\Core\Entity\Health\Report\Finding;
use Concrete\Core\Health\Report\Finding\Control\DropdownControl;
use Concrete\Core\Health\Report\Finding\Control\DropdownItemControl;
use Concrete\Core\Health\Report\Finding\Message\MessageHasDetailsInterface;

class SearchResultExporter implements ExporterInterface
{

    public function getColumns(): array
    {
        return [
            new Column('type', t('Type')),
            new Column('message', t('Message')),
            new Column('url', t('URL')),
            new Column('content', t('Content')),
        ];
    }

    public function getColumnValue(ColumnInterface $column, Finding $finding): string
    {
        if ($column->getKey() === 'type') {
            return $finding->getFormatter()->getType();
        }
        if ($column->getKey() === 'message') {
            $message = $finding->getMessage();
            return $message->getFormatter()->getFindingsListMessage($message, $finding);
        }
        if ($column->getKey() === 'url') {
            $control = $finding->getControl();
            if ($control instanceof DropdownControl) {
                foreach ($control->getControls() as $subControl) {
                    if ($subControl instanceof DropdownItemControl) {
                        $location = $subControl->getLocation();
                        return $location->getUrl();
                    }
                }
            }
        }
        if ($column->getKey() === 'content') {
            $message = $finding->getMessage();
            $formatter = $message->getFormatter();
            if ($formatter instanceof MessageHasDetailsInterface) {
                return $formatter->getDetailsString($message);
            }
        }
        return '';
    }
}
