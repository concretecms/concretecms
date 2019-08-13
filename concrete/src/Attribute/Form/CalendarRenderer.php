<?php
namespace Concrete\Core\Attribute\Form;

use Concrete\Core\Form\Context\ContextInterface;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Entity\Calendar;

/**
 * @since 8.3.0
 */
class CalendarRenderer extends Renderer
{

    protected $calendar;

    public function __construct(Calendar $calendar, ContextInterface $context, ObjectInterface $object = null)
    {
        $this->calendar = $calendar;
        parent::__construct($context, $object);
    }

}
