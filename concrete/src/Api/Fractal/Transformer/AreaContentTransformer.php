<?php
namespace Concrete\Core\Api\Fractal\Transformer;

use Concrete\Core\Area\Area;
use Concrete\Core\Http\Request;
use Concrete\Core\Area\ApiArea;
use Concrete\Core\Api\Fractal\Transformer\Traits\SanitizableContentTrait;
use League\Fractal\TransformerAbstract;

class AreaContentTransformer extends TransformerAbstract
{

    use SanitizableContentTrait;

    public function transform(ApiArea $area)
    {
        $request = Request::getInstance();
        $request->setCustomRequestUser(null);
        $request->setCurrentPage($area->getPage());

        $contents = '';
        ob_start();
        (new Area($area->getAreaHandle()))->display($area->getPage());
        $contents = ob_get_contents();
        ob_end_clean();

        $contentSanitized = $this->stripAllTags($contents);
        return [
            'raw' => $contents,
            'content' => $contentSanitized,
        ];
    }

}