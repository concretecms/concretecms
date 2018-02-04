<?php
namespace Concrete\Core\Express\Association\Formatter;

class ManyToOneFormatter extends AbstractFormatter
{
    public function getIcon()
    {
        return '<i class="fa fa-cubes"></i><i class="fa fa-arrow-right"></i> <i class="fa fa-cube"></i>';
    }

    public function getTypeDisplayName()
    {
        return t('Many-To-One');
    }
}
