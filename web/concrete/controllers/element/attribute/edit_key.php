<?php
namespace Concrete\Controller\Element\Attribute;

use Concrete\Core\Attribute\Type;
use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\AttributeKey\AttributeKey;

class EditKey extends Form
{

    protected $key;

    public function __construct(AttributeKey $key)
    {
        $this->key = $key;
        parent::__construct($key->getAttributeType());
    }

    public function view()
    {
        $this->set('key', $this->key);
        parent::view();
    }



}
