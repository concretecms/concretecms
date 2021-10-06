<?php
namespace Concrete\Core\Express\Association\Formatter;

class OneToManyFormatter extends AbstractFormatter
{
    public function getIcon()
    {
        return '<i class="fas fa-cube"></i><i class="fas fa-arrow-right"></i> <i class="fas fa-cubes"></i>';
    }

    public function getTypeDisplayName()
    {
        return t('One-To-Many');
    }
}
