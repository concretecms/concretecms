<?php
namespace Concrete\Core\Express\Exception;

class NoNamespaceDefinedException extends \Exception
{

    public function __construct()
    {
        $this->message = t('No namespace has been defined for your entity class.');
    }

}
