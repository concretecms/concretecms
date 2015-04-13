<?php
namespace Concrete\Core\Application\Service\UserInterface\Help;

class Message implements MessageInterface
{
    protected $content;
    protected $guide;
    protected $link;
    protected $identifier;

    /**
     * @return mixed
     */
    public function getContent()
    {
        $content = new \HtmlObject\Element('div');
        $content->setChild(new \HtmlObject\Element('p', $this->content));
        if ($this->link) {
            $content->setChild(id(new \HtmlObject\Link($this->link, t('Learn More')))->target('blank'));
        }
        if ($this->guide) {
            $button = new \HtmlObject\Link($this->link, t('Launch Guide'));
            $button->addClass('btn btn-info btn-sm')
                ->href('#')
                ->setAttribute('data-launch-guide', $this->guide);
            $content->setChild($button);
        }

        return $content;
    }

    /**
     * @param mixed $content
     */
    public function setMessageContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param mixed $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    public function addGuide($guide)
    {
        $this->guide = $guide;
    }

    /**
     * @param mixed $content
     */
    public function addLearnMoreLink($link)
    {
        $this->link = $link;
    }

}
