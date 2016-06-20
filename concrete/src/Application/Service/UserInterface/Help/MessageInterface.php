<?php
namespace Concrete\Core\Application\Service\UserInterface\Help;

interface MessageInterface
{
    /**
     * @return \HtmlObject\Element
     */
    public function getContent();

    /**
     * @return string
     */
    public function getIdentifier();
}
