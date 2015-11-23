<?php
namespace Concrete\Core\Express\Definition;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\AttributeKeyFactoryInterface;
use \Concrete\Core\Entity\Express\Entity;

class PrimaryKeyField extends Field
{

    protected $name = 'id';
    protected $options = array('autoincrement');
    protected $type = 'integer';


}