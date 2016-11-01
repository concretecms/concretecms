<?php
namespace Concrete\Core\Express\Form;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;

interface FormRendererInterface
{
    function getRequiredHtmlElement();
}
