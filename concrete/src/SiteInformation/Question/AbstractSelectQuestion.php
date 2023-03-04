<?php

namespace Concrete\Core\SiteInformation\Question;

use HtmlObject\Element;

abstract class AbstractSelectQuestion extends AbstractQuestion
{

    abstract public function getOptions(): array;

    public function getTag(array $results): Element
    {
        $wrapper = new Element('div', null, ['class' => 'mb-3']);
        $wrapper->appendChild(
            (new Element('label', null, ['for' => 'q_' . $this->getKey(), 'class' => 'form-label']))->setValue(
                $this->getLabel()
            )
        );
        $select = new Element(
            'select',
            null,
            [
                'name' => $this->getKey(),
                'id' => 'q_' . $this->getKey(),
                'required' => 'required',
                'class' => 'form-select'
            ]
        );
        $select->appendChild((new Element('option'))->setAttribute('value', '')->setValue('** Choose'));
        foreach ($this->getOptions() as $key => $value) {
            $option = (new Element('option'))->setAttribute('value', $key)->setValue($value);
            if (isset($results[$this->getKey()]) && $results[$this->getKey()] === $key) {
                $option->selected(true);
            }
            $select->appendChild($option);
        }
        $wrapper->appendChild($select);
        return $wrapper;
    }
}
