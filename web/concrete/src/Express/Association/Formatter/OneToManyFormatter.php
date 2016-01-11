<?php
namespace Concrete\Core\Express\Association\Formatter;

class OneToManyFormatter extends AbstractFormatter
{
    public function getIcon()
    {
        return '<i class="fa fa-cube"></i><i class="fa fa-arrow-right"></i> <i class="fa fa-cubes"></i>';
    }

    public function getTypeDisplayName()
    {
        return t('One-To-Many');
    }
}
