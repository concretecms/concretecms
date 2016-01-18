<?php
namespace Concrete\Attribute\Text;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Attribute\DefaultController;

class Controller extends DefaultController
{
    protected $searchIndexFieldDefinition = array(
        'type' => 'text',
        'options' => array('length' => 4294967295, 'default' => null, 'notnull' => false),
    );

    public function form()
    {
        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->app->make('helper/text')->entities($this->getAttributeValue()->getValue());
        }
        echo $this->app->make('helper/form')->text($this->field('value'), $value);
    }

    public function composer()
    {
        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->app->make('helper/text')->entities($this->getAttributeValue()->getValue());
        }
        echo $this->app->make('helper/form')->text($this->field('value'), $value, array('class' => 'span5'));
    }

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('file-text');
    }
}
