<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine;

interface RoutineInterface
{
    /**
     * Get a handle that uniquely identifies this routing.
     *
     * @return string
     */
    public function getHandle();

    /**
     * Extracts the items contained in a content.
     *
     * @param string|mixed $content
     *
     * @return \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface[]
     */
    public function match($content);

    /**
     * Replace the content with tie items found in it.
     *
     * @param string|mixed $content
     *
     * @return string|mixed
     */
    public function replaceContent($content);
}
