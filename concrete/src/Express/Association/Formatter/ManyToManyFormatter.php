<?php
namespace Concrete\Core\Express\Association\Formatter;

class ManyToManyFormatter extends AbstractFormatter
{
    public function getIcon()
    {
        return '<i class="fas fa-cubes"></i><i class="fas fa-arrow-right"></i> <i class="fas fa-cubes"></i>';
    }

    public function getTypeDisplayName()
    {
        return t('Many-To-Many');
    }
}
