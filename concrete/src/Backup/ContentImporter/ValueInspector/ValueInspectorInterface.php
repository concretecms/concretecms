<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector;

/**
 * @since 5.7.5.4
 */
interface ValueInspectorInterface
{
    public function inspect($content);
}
