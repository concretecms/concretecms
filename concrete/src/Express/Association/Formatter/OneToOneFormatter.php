<?php
namespace Concrete\Core\Express\Association\Formatter;

class OneToOneFormatter extends AbstractFormatter
{
    public function getIcon()
    {
        return '<i class="fas fa-cube"></i><i class="fas fa-arrow-right"></i> <i class="fas fa-cube"></i>';
    }

    public function getTypeDisplayName()
    {
        return t('One-To-One');
    }
}
