<?php
namespace Concrete\Core\Site\Type\Formatter;

use Concrete\Core\Entity\Site\Type;

abstract class AbstractFormatter implements FormatterInterface
{

    protected $type;

    /**
     * AbstractFormatter constructor.
     * @param $type
     */
    public function __construct(Type $type)
    {
        $this->type = $type;
    }



}