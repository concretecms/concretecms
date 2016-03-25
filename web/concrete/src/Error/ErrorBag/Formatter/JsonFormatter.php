<?php
namespace Concrete\Core\Error\ErrorBag\Formatter;

use Concrete\Core\Error\ErrorBag\ErrorBag;

class JsonFormatter extends AbstractFormatter
{

    public function output()
    {
       echo json_encode($this->asArray());
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
