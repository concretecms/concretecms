<?php
namespace Concrete\Controller\Element\Attribute;

use Concrete\Core\Attribute\Type;
use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Attribute\Key\Key;

class EditKey extends Form
{

    protected $key;

    protected $dashboard_page_submit_action = 'update';

    public function __construct(Key $key)
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
