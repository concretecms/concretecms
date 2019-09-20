<?php
namespace Concrete\Attribute\Telephone;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Attribute\DefaultController;

/**
 * @since 8.0.0
 */
class Controller extends DefaultController
{
    /**
     * @since 8.0.0 visibility: protected
     * @since 8.2.0 visibility: public
     */
    public $helpers = ['form'];

    public function form()
    {
        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->app->make('helper/text')->entities($this->getAttributeValue()->getValue());
        }
        $this->set('value',$value);
    }

    public function composer()
    {
        $this->form();
    }

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('phone');
    }
}
