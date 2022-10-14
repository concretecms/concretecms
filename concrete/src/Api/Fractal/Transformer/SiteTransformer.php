<?php
namespace Concrete\Core\Api\Fractal\Transformer;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Api\Resources;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class SiteTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'locales',
        'custom_attributes',
    ];

    public function transform(Site $site)
    {
        $data['id'] = $site->getSiteID();
        $data['handle'] = $site->getSiteHandle();
        $data['name'] = $site->getSiteName();
        $data['home_page_id'] = $site->getSiteHomePageID();

        $defaultLocale = $site->getDefaultLocale();
        if ($defaultLocale) {
            $defaultLocaleString = $defaultLocale->getLocale();
        }
        
        $data['default_locale'] = $defaultLocaleString;
        return $data;
    }

    public function includeLocales(Site $site)
    {
        $locales = $site->getLocales();
        return new Collection($locales, new SiteLocaleTransformer(), Resources::RESOURCE_LOCALES);
    }

    public function includeCustomAttributes(Site $site)
    {
        $values = $site->getObjectAttributeCategory()->getAttributeValues($site);
        return new Collection($values, new AttributeValueTransformer(), Resources::RESOURCE_CUSTOM_ATTRIBUTES);
    }

}
