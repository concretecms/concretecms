<?php
namespace Concrete\Core\Express\Entry;

class PublicIdentifierGenerator
{

    public function generate()
    {
        return str_replace('.', '', uniqid('', true));
    }

}
