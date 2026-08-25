<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine;

use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\FileItem;

class FileRoutine extends AbstractRegularExpressionRoutine
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\RoutineInterface::getHandle()
     */
    public function getHandle()
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\AbstractRegularExpressionRoutine::getRegularExpression()
     */
    public function getRegularExpression()
    {
        return '/{ccm:export:file:(.*?)\}/i';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\AbstractRegularExpressionRoutine::getItem()
     */
    public function getItem($identifier)
    {
        $filename = '';
        $prefix = null;
        $id = '';
        if (preg_match('/^(?:(.*):)?id=(' . FileItem::IDENTIFIER_REGEX . ')$/Di', $identifier, $m)) {
            $identifier = $m[1] ?? '';
            $id = FileItem::parseFileIdentifier($m[2]);
        }
        if ($identifier !== '') {
            [$prefix, $filename] = str_contains($identifier, ':') ? explode(':', $identifier, 2) : [null, $identifier];
            $id = '';
        }

        return new FileItem($filename, $prefix, $id);
    }
}
