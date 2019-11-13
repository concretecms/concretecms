<?php
namespace Concrete\Core\Summary\Data\Field;

interface DataFieldInterface
{
    
    public function getFieldIdentifier() : string;
    
    public function getData() : DataFieldDataInterface;

}
