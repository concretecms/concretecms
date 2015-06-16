<?php
namespace Concrete\Core\Page\Stack;

use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Type\Type;
use \Config;
use \Core;
use \Page;

class StackCategory
{

    protected $page;

    public function __construct($page)
    {
        $this->setPage($page);
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param mixed $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @param Section $section
     * @return StackCategory
     */
    public static function getCategoryFromMultilingualSection(Section $section)
    {
        $sc = null;
        if ($section->isDefaultMultilingualSection()) {
            return static::getFromDefaultMultilingualSection();
        } else {
            // we find the stack category located at /paths/locale
            $text = Core::make('helper/text');
            $locale = $section->getLocale();
            $path = STACKS_PAGE_PATH . '/' . $locale;
            $page = \Page::getByPath($path);
            if (is_object($page) && !$page->isError()) {
                $sc = new StackCategory($page);
            }
        }
        return $sc;
    }

    /**
     * @return StackCategory
     */
    public static function getFromDefaultMultilingualSection()
    {
        $sc = new StackCategory(Page::getByPath(STACKS_PAGE_PATH));
        return $sc;
    }

    public function copyToTargetCategory(StackCategory $category)
    {
        $list = new StackList();
        $list->filterByStackCategory($this);
        $stacks = $list->get();
        foreach($stacks as $stack) {
            $stack->duplicate($category->getPage());
        }
    }

    /**
     * @param Section $section
     * @return StackCategory
     */
    public static function createFromMultilingualSection(Section $section)
    {
        $parent = \Page::getByPath(STACKS_PAGE_PATH);
        $data = array();
        $data['name'] = $section->getLocale();
        $data['cHandle'] = $section->getLocale();
        $type = Type::getByHandle(STACK_CATEGORY_PAGE_TYPE);
        $page = $parent->add($type, $data);

        $sc = new StackCategory($page);
        return $sc;
    }

}
