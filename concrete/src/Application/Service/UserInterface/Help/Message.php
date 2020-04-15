<?php
namespace Concrete\Core\Application\Service\UserInterface\Help;

class Message implements MessageInterface
{
    /**
     * The message content, in HTML format.
     *
     * @var string
     */
    protected $content;

    /**
     * The handle of the tour guide.
     *
     * @var string|null
     */
    protected $guide;

    /**
     * The link to a video/external URL.
     *
     * @var string|null
     */
    protected $link;

    /**
     * The message identifier.
     *
     * @var string
     */
    protected $identifier;

    /**
     * @return \HtmlObject\Element
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
     * @param string|null $content
     */
    public function setMessageContent($content)
    {
        $this->content = $content;
    }

    public function getMessageContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return string|null
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param string|null $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @param string|null $guide
     */
    public function addGuide($guide)
    {
        $this->guide = $guide;
    }

    public function getGuide(): ?string
    {
        return $this->guide;
    }

    /**
     * @param string|null $link
     */
    public function addLearnMoreLink($link)
    {
        $this->link = $link;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }
}
