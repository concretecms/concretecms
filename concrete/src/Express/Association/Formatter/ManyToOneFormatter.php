<?php
namespace Concrete\Core\Express\Association\Formatter;

class ManyToOneFormatter extends AbstractFormatter
{
    public function getIcon()
    {
        return '<i class="fas fa-cubes"></i><i class="fas fa-arrow-right"></i> <i class="fas fa-cube"></i>';
    }

    public function getTypeDisplayName()
    {
        return t('Many-To-One');
    }
}
