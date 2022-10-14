<?php
namespace Concrete\Core\Api\Fractal\Transformer;

use Carbon\Carbon;
use Concrete\Core\Api\Fractal\Transformer\Traits\GetPageApiAreasTrait;
use Concrete\Core\Api\Resources;
use Concrete\Core\Entity\File\File;
use Concrete\Core\File\File as CoreFile;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\Page\Page;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class PageTransformer extends TransformerAbstract
{

    use GetPageApiAreasTrait;

    protected $defaultIncludes = [
        'version',
    ];

    protected $availableIncludes = [
        'custom_attributes',
        'areas',
        'files',
        'content',
    ];

    public function transform(Page $page)
    {
        $data['id'] = $page->getCollectionID();
        $data['path'] = $page->getCollectionPath();
        $data['name'] = $page->getCollectionName();
        $data['date_added'] = Carbon::make($page->getCollectionDateAdded())->toAtomString();
        $data['date_last_updated']  = Carbon::make($page->getCollectionDateLastModified())->toAtomString();
        $data['type'] = $page->getPageTypeHandle();
        $data['template'] = $page->getPageTemplateHandle();

        $config = app('config');
        $site = $page->getSite();
        $locale = $config->get('concrete.locale');
        if ($site) {
            $siteConfig = $site->getConfigRepository();
            $locale = $siteConfig->get('multilingual.default_source_locale');
            $tree = $page->getSiteTreeObject();
            if ($tree) {
                $treeLocale = $tree->getLocale();
                if ($treeLocale) {
                    $locale = (string)$treeLocale->getLocale();
                }
            }
        }
        
        $data['locale'] = $locale;
        $data['description'] = (string) $page->getCollectionDescription();

        if ($page->isExternalLink()) {
            $data['external_link_url'] = $page->getCollectionPointerExternalLink();
        }

        return $data;
    }

    public function includeCustomAttributes(Page $page)
    {
        $values = $page->getObjectAttributeCategory()->getAttributeValues($page);
        return new Collection($values, new AttributeValueTransformer(), Resources::RESOURCE_CUSTOM_ATTRIBUTES);
    }

    public function includeAreas(Page $page)
    {
        $areas = $this->getAreas($page);
        return new Collection($areas, new AreaTransformer());
    }

    public function includeVersion(Page $page)
    {
        $version = $page->getVersionObject();
        return new Item($version, new CollectionVersionTransformer(), Resources::RESOURCE_PAGE_VERSIONS);
    }

    public function includeFiles(Page $page)
    {
        $allBlocks = $page->getBlocks();
        $allAttributes = $page->getObjectAttributeCategory()->getAttributeValues($page);

        $records = [];
        foreach ($allBlocks as $block) {
            $controller = $block->getController();
            if ($controller instanceof FileTrackableInterface) {
                $records = array_merge($records, $controller->getUsedFiles());
            }
        }

        foreach ($allAttributes as $attribute) {
            $controller = $attribute->getController();
            if ($controller instanceof FileTrackableInterface) {
                $records = array_merge($records, $controller->getUsedFiles());
            }
        }

        $files = [];
        foreach ($records as $record) {
            if ($record instanceof File) {
                $files[] = $record;
            } else if (uuid_is_valid($record)) {
                $file = CoreFile::getByUUID($record);
                if ($file) {
                    $files[] = $file;
                }
            } else {
                $file = CoreFile::getByID($record);
                if ($file) {
                    $files[] = $file;
                }
            }
        }

        return new Collection($files, new FileTransformer(), Resources::RESOURCE_FILES);
    }

    /**
     * Attempts to include a representation of the page's content, not including header and footer. Basically
     * all content, in a renderable format, in the proper order of the page. Historically that has been difficult
     * because Concrete has no idea what order your pages areas appear in, but we're going to do our best.
     *
     * @param Page $page
     */
    public function includeContent(Page $page)
    {
        return new Item($page, new PageContentTransformer());
    }
}
