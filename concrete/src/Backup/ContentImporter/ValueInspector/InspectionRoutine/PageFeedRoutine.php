<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine;

use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageFeedItem;

class PageFeedRoutine extends AbstractRegularExpressionRoutine
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\RoutineInterface::getHandle()
     */
    public function getHandle()
    {
        return 'page_feed';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\AbstractRegularExpressionRoutine::getRegularExpression()
     */
    public function getRegularExpression()
    {
        return '/{ccm:export:pagefeed:(.*?)\}/i';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\AbstractRegularExpressionRoutine::getItem()
     */
    public function getItem($identifier)
    {
        return new PageFeedItem($identifier);
    }
}
