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
        $id = null;
        if (preg_match('/^(.*):id=([1-9]\d*)$/D', $identifier, $m)) {
            $identifier = $m[1];
            $id = (int) $m[2];
        }
        if ($identifier !== '') {
            [$prefix, $filename] = str_contains($identifier, ':') ? explode(':', $identifier, 2) : [null, $identifier];
            $id = null;
        }

        return new FileItem($filename, $prefix, $id);
    }
}
