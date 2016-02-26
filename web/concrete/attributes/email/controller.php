<?php
namespace Concrete\Attribute\Email;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Attribute\DefaultController;

class Controller extends DefaultController
{

    public function form()
    {
        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->app->make('helper/text')->entities($this->getAttributeValue()->getValue());
        }
        echo $this->app->make('helper/form')->email($this->field('value'), $value);
    }

    public function composer()
    {
        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->app->make('helper/text')->entities($this->getAttributeValue()->getValue());
        }
        echo $this->app->make('helper/form')->email($this->field('value'), $value, array('class' => 'span5'));
    }

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('envelope');
    }
}
