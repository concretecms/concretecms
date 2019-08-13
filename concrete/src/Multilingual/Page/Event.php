<?php
namespace Concrete\Core\Multilingual\Page;

use Concrete\Core\Page\Event as PageEvent;

/**
 * @since 5.7.3
 */
class Event extends PageEvent
{
    protected $locale;

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }
}
