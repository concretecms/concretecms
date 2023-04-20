<?php
namespace Concrete\Core\Api\Fractal\Transformer;

use Concrete\Core\Entity\Site\Locale;
use League\Fractal\TransformerAbstract;

class SiteLocaleTransformer extends TransformerAbstract
{

    public function transform(Locale $locale)
    {
        $homePageID = null;
        $tree = $locale->getSiteTreeObject();
        if ($tree) {
            $homePageID = $tree->getSiteHomePageID();
        }
        return [
            'id' => $locale->getLocaleID(),
            'country' => $locale->getCountry(),
            'language' => $locale->getLanguage(),
            'home_page_id' => $homePageID,
        ];
    }

}
