<?php
namespace Concrete\Core\Page;

/**
 * @since 5.7.5
 */
class DeletePageEvent extends Event
{
    protected $proceed = true;

    public function cancelDelete()
    {
        $this->proceed = false;
    }

    public function proceed()
    {
        return $this->proceed;
    }
}
