<?php

namespace Concrete\Core\Application\UserInterface\Welcome\Modal;

use HtmlObject\Element;

interface BasicModalInterface extends ModalInterface
{

    public function getTitle(): string;

    public function getBody(): string;

}
