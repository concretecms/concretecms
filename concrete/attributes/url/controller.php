<?php
namespace Concrete\Attribute\Url;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Attribute\DefaultController;

class Controller extends DefaultController
{

    public $helpers = array('form');

    public function form()
    {
        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->app->make('helper/text')->entities($this->getAttributeValue()->getValue());
        }
        $this->set('value', $value);
    }

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('link');
    }
}
