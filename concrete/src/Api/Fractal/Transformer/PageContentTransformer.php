<?php
namespace Concrete\Core\Api\Fractal\Transformer;

use Concrete\Core\Api\Fractal\Transformer\Traits\SanitizableContentTrait;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Page;
use League\Fractal\TransformerAbstract;
use Sunra\PhpSimple\HtmlDomParser;

class PageContentTransformer extends TransformerAbstract
{

    use SanitizableContentTrait;

    public function transform(Page $page)
    {
        $request = Request::getInstance();
        $request->setCustomRequestUser(null);
        $request->setCurrentPage($page);

        $controller = $page->getPageController();
        $view = $controller->getViewObject();
        $contents = $view->render();

        // Return only inside .ccm-page
        $pageContent = '';
        $dom = HtmlDomParser::str_get_html($contents);
        $element = $dom->find('.ccm-page');
        if (isset($element[0])) {
            $pageContent = $element[0]->innertext();
            $pageContentSanitized = $this->stripAllTags($pageContent);
        }

        // Sanitize inside ccm-page
        return [
            'raw' => $pageContent,
            'content' => $pageContentSanitized,
        ];
    }


}
