<?php
namespace Concrete\Core\Error\ErrorBag\Formatter;

use Concrete\Core\Error\ErrorBag\ErrorBag;

class StandardFormatter extends AbstractFormatter
{

    public function getString()
    {
        $html = '';
        if ($this->error->has()) {
            $html .= '<ul class="ccm-error">';
            foreach ($this->error->getList() as $error) {
                $html .= '<li>' . $error . '</li>';
            }
            $html .= '</ul>';
        }
        return $html;
    }

    public function output()
    {
        echo $this->getString();
    }



}
