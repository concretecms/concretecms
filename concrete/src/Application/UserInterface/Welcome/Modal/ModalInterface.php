<?php

namespace Concrete\Core\Application\UserInterface\Welcome\Modal;

use HtmlObject\Element;

interface ModalInterface
{

    public function getModalElement(): Element;

    public function __toString(): string;

}
