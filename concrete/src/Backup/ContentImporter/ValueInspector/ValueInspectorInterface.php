<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector;

interface ValueInspectorInterface
{
    /**
     * Inspect a content.
     *
     * @param string|mixed $content
     *
     * @return \Concrete\Core\Backup\ContentImporter\ValueInspector\ResultInterface
     */
    public function inspect($content);
}
