<?php
namespace Concrete\Core\Multilingual\Page;

use Concrete\Core\Page\Event as PageEvent;

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
