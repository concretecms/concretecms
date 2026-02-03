<?php
namespace Concrete\Core\Sharing\OpenGraph;

use Concrete\Core\Entity\File\File;
use Concrete\Core\Page\Page;
use Concrete\Core\Site\Config\Liaison;
use Concrete\Core\Site\Service;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use HtmlObject\Element;

class OpenGraph
{
    public const TAG_OG_SITE_NAME = 'og:site_name';
    public const TAG_OG_URL = 'og:url';
    public const TAG_OG_TITLE = 'og:title';
    public const TAG_OG_DESCRIPTION = 'og:description';
    public const TAG_OG_IMAGE_URL = 'og:image:url';
    public const TAG_OG_IMAGE_SECURE_URL = 'og:image:secure_url';
    public const TAG_OG_IMAGE_TYPE = 'og:image:type';
    public const TAG_OG_IMAGE_WIDTH = 'og:image:width';
    public const TAG_OG_IMAGE_HEIGHT = 'og:image:height';
    public const TAG_OG_TYPE = 'og:type';
    public const TAG_OG_LOCALE = 'og:locale';
    public const TAG_OG_VIDEO = 'og:video';
    public const TAG_OG_VIDEO_URL = 'og:video:url';
    public const TAG_OG_VIDEO_SECURE_URL = 'og:video:secure_url';
    public const TAG_OG_VIDEO_TYPE = 'og:video:type';
    public const TAG_OG_VIDEO_WIDTH = 'og:video:width';
    public const TAG_OG_VIDEO_HEIGHT = 'og:video:height';
    public const TAG_FB_APP_ID = 'fb:app_id';

    /**
     * @var Liaison
     */
    protected $config;

    /**
     * @var Service
     */
    protected $siteService;

    /**
     * @var \Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface
     */
    protected $resolverManager;

    public function __construct(Service $siteService, ResolverManagerInterface $resolverManager)
    {
        $this->siteService = $siteService;
        $this->config = $siteService->getSite()->getConfigRepository();
        $this->resolverManager = $resolverManager;
    }

    /**
     * @param string $property
     * @param string $content
     * @return Element
     */
    private function createTag(string $property, string $content): Element
    {
        $element = new Element('meta');
        $element->setIsSelfClosing(true);
        $element->property($property);
        $element->content(htmlspecialchars($content, ENT_QUOTES, APP_CHARSET));
        return $element;
    }

    /**
     * @param Page $page
     * @param $configField
     * @param $ogField
     * @return Element|null
     */
    private function createTagFromConfig(Page $page, $configField, $ogField): ?Element
    {
        $field = $this->config->get('social.opengraph.' . $configField);
        if (is_string($field) && !empty($field)) {
            return $this->createTag($ogField, $field);
        }
        $valueFrom = $field['value_from'] ?? null;
        $content = null;
        if ($valueFrom) {
            switch ($valueFrom) {
                case 'page_attribute':
                    $content = (string) $page->getAttribute($field['attribute']);
                    break;
                case 'page_property':
                    switch ($field['property']) {
                        case 'title':
                            $content = $page->getCollectionName();
                            break;
                        case 'description':
                            $content = $page->getCollectionDescription();
                            break;
                    }
                    break;
                default: // value;
                    $content = $field['value'] ?? '';
                    break;
            }
        }
        if (!empty($content)) {
            return $this->createTag($ogField, $content);
        }
        return null;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool) $this->config->get('social.opengraph.enabled');
    }

    /**
     * @return Element[]
     */
    public function getTags(Page $page): array
    {
        $site = $this->siteService->getSite();
        $tags = [];
        $tags[] = $this->createTag(self::TAG_OG_SITE_NAME, $site->getSiteName());
        $tree = $page->getSiteTreeObject();
        if ($tree) {
            $tags[] = $this->createTag(self::TAG_OG_LOCALE, $tree->getLocale()->getLocale());
        }
        if ($tag = $this->createTagFromConfig($page, 'field_og_type', self::TAG_OG_TYPE)) {
            $tags[] = $tag;
        }
        if ($tag = $this->createTagFromConfig($page, 'field_og_title', self::TAG_OG_TITLE)) {
            $tags[] = $tag;
        }
        if ($tag = $this->createTagFromConfig($page, 'field_og_description', self::TAG_OG_DESCRIPTION)) {
            $tags[] = $tag;
        }
        if ($tag = $this->createTagFromConfig($page, 'field_fb_app_id', self::TAG_FB_APP_ID)) {
            $tags[] = $tag;
        }
        if ($site->getSiteCanonicalURL()) {
            $tags[] = $this->createTag(self::TAG_OG_URL, (string) $this->resolverManager->resolve([$page]));
        }
        $imageAttribute = $this->config->get('social.opengraph.field_og_thumbnail');
        if ($imageAttribute['value_from'] === 'page_attribute') {
            $image = $page->getAttribute($imageAttribute['attribute']);
            if ($image instanceof File) {
                $width = $image->getAttribute('width');
                $height = $image->getAttribute('height');
                $tags[] = $this->createTag(self::TAG_OG_IMAGE_URL, $image->getURL());
                $tags[] = $this->createTag(self::TAG_OG_IMAGE_TYPE, $image->getMimeType());
                if ($width) {
                    $tags[] = $this->createTag(self::TAG_OG_IMAGE_WIDTH, $width);
                }
                if ($height) {
                    $tags[] = $this->createTag(self::TAG_OG_IMAGE_HEIGHT, $height);
                }
            }
        }

        return $tags;
    }
}
