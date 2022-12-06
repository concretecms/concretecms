<?php

namespace Concrete\Core\Application\UserInterface\Welcome\Modal;

use HtmlObject\Element;

abstract class AbstractBasicModal implements BasicModalInterface
{

    public function getModalElement(): Element
    {
        $modal = new Element('concrete-welcome-modal');

        $title = new Element('template');
        $title->setAttribute('v-slot:title', 'title');
        $title->setValue($this->getTitle());

        $body = new Element('template');
        $body->setAttribute('v-slot:body', 'body');
        $body->setValue($this->getBody());

        $modal->appendChild($title);
        $modal->appendChild($body);
        return $modal;
    }

    public function __toString(): string
    {
        return (string) $this->getModalElement();
    }

}