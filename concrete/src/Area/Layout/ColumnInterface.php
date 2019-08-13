<?php
namespace Concrete\Core\Area\Layout;

/**
 * @since 5.7.5
 */
interface ColumnInterface
{
    public function getColumnHtmlObject();
    public function getColumnHtmlObjectEditMode();
}
