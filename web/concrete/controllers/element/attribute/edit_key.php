<?php
namespace Concrete\Controller\Element\Attribute;

use Concrete\Core\Attribute\AttributeKeyInterface;

class EditKey extends Form
{
    protected $key;

    protected $dashboard_page_submit_action = 'update';

    public function __construct(AttributeKeyInterface $key)
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
