<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector;


use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\FileItem;
use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageFeedItem;
use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageItem;
use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageTypeItem;
use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PictureItem;

class ValueInspector
{
    protected $content;

    protected $regExp = '/\<concrete-picture[^>]* file="([^"]*)"|\{ccm:export:page:(.*?)\}|\{ccm:export:file:(.*?)\}|\{ccm:export:pagetype:(.*?)\}|\{ccm:export:pagefeed:(.*?)\}/i';

    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getMatchedItem()
    {
        $items = $this->getMatchedItems();
        if (isset($items[0])) {
            return $items[0];
        }
    }

    protected function getItemObjectFromIndex($i, $reference)
    {
        switch($i) {
            case 1:
                $o = new PictureItem($reference);
                break;
            case 2:
                $o = new PageItem($reference);
                break;
            case 3:
                $o = new FileItem($reference);
                break;
            case 4:
                $o = new PageTypeItem($reference);
                break;
            case 5:
                $o = new PageFeedItem($reference);
                break;
        }
        return $o;
    }

    /**
     * Iterates through the $content in the class, and returns an array of matched ItemInterface objects
     * @return \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface[]
     */
    public function getMatchedItems()
    {
        $items = array();
        if (preg_match_all(
            $this->regExp,
            $this->content,
            $matches
        )
        ) {
            if (count($matches)) {
                for ($i = 1; $i < count($matches); $i++ ) {
                    $results = $matches[$i];
                    foreach($results as $reference) {
                        if ($reference) {
                            $o = $this->getItemObjectFromIndex($i, $reference);
                            $items[] = $o;
                        }
                    }
                }
            }
        }
        return $items;
    }

    /**
     * Replaces the content with the matched content items' content value. Content value is the value that the matched
     * content item places when in a block of HTML/rich text content. This is separate from the value that is
     * returned when importing into a field.
     */
    public function getReplacedContent()
    {
        $text = preg_replace_callback(
            $this->regExp,
            function ($matches) {
                for ($i = 1; $i < count($matches); $i++ ) {
                    if ($matches[$i]) {
                        $o = $this->getItemObjectFromIndex($i, $matches[$i]);
                        return $o->getContentValue();
                    }
                }

            },
            $this->content
        );
        return $text;
    }
}
