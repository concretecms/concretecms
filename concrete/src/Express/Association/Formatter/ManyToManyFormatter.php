<?php
namespace Concrete\Core\Express\Association\Formatter;

/**
 * @since 8.0.0
 */
class ManyToManyFormatter extends AbstractFormatter
{
    public function getIcon()
    {
        return '<i class="fa fa-cubes"></i><i class="fa fa-arrow-right"></i> <i class="fa fa-cubes"></i>';
    }

    public function getTypeDisplayName()
    {
        return t('Many-To-Many');
    }
}
