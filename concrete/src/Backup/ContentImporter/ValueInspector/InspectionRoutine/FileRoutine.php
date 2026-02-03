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
        if (str_contains($identifier, ':')) {
            [$prefix, $filename] = explode(':', $identifier);
        } else {
            $filename = $identifier;
            $prefix = null;
        }

        return new FileItem($filename, $prefix);
    }
}
