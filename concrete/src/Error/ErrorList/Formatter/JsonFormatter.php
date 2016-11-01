<?php
namespace Concrete\Core\Error\ErrorList\Formatter;

use Concrete\Core\Error\ErrorList\ErrorList;

class JsonFormatter extends AbstractFormatter
{

    public function render()
    {
       return json_encode($this->asArray());
    }

    public function asArray()
    {
        $error = $this->error;
        if ($error->has()) {
            $o = array();
            $o['error'] = true;
            $o['errors'] = array();
            foreach ($error->getList() as $error) {
                $o['errors'][] = (string) $error;
            }
            return $o;
        }
    }


}
