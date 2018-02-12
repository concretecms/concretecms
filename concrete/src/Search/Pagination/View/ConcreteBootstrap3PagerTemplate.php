<?php
namespace Concrete\Core\Search\Pagination\View;

use Pagerfanta\View\Template\Template;
use Pagerfanta\View\Template\TwitterBootstrap3Template;

class ConcreteBootstrap3PagerTemplate extends Template
{

    static protected $defaultOptions = array(
        'css_container_class' => 'pagination',
    );

    public function container()
    {
        return sprintf('<div class="ccm-pagination-wrapper"><ul class="%s">%%pages%%</ul></div>',
            $this->option('css_container_class')
        );
    }

    public function page($page)
    {
        return null;
    }

    public function pageWithText($page, $text)
    {
        return null;
    }

    public function previousDisabled()
    {
        return '<li class="disabled pull-left"><a href="#" disabled="disabled" onclick="return false">' . t('Previous') . '</a></li>';
    }

    public function previousEnabled($page)
    {
        $href = $this->generateRoute($page);
        return '<li class="pull-left"><a href="' . $href . '">' . t('Previous') . '</a></li>';
    }

    public function nextDisabled()
    {
        return '<li class="disabled pull-right"><a href="#" disabled="disabled" onclick="return false">' . t('Next') . '</a></li> ';
    }

    public function nextEnabled($page)
    {
        $href = $this->generateRoute($page);
        return '<li class="pull-right"><a href="' . $href . '">' . t('Next'). '</a></li> ';
    }

    public function last($page)
    {
        return null;
    }

    public function separator()
    {
        return null;
    }

    public function current($page)
    {
        $href = $this->generateRoute($page);
        $pageNumber = 1;
        $pageResult = '/ccm_cursor=([\d\|]+)/';
        if (preg_match_all($pageResult, $href, $pageResultMatches)) {
            $page = '/(\d+)/';
            if (preg_match_all($page, $pageResultMatches[1][0], $pageMatches)) {
                $pageNumber = count($pageMatches[1]) + 1;
            }
        }

        return '<li class="active"><span>' . $pageNumber . '</span></li>';
    }

    public function first()
    {
        return null;
    }
}
