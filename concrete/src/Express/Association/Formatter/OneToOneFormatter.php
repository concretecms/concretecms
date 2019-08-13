<?php
namespace Concrete\Core\Express\Association\Formatter;

/**
 * @since 8.0.0
 */
class OneToOneFormatter extends AbstractFormatter
{
    public function getIcon()
    {
        return '<i class="fa fa-cube"></i><i class="fa fa-arrow-right"></i> <i class="fa fa-cube"></i>';
    }

    public function getTypeDisplayName()
    {
        return t('One-To-One');
    }
}
