<?php
namespace Concrete\Core\Application\Service\UserInterface\Help;

/**
 * @since 5.7.4
 */
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
