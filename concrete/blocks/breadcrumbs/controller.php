<?php

namespace Concrete\Block\Breadcrumbs;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Navigation\Breadcrumb\PageBreadcrumbFactory;
use Concrete\Core\Page\Page;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController implements UsesFeatureInterface
{
    public $helpers = ['form'];
    public $includeCurrent;
    public $ignoreExcludeNav;
    public $ignorePermission;
    protected $btInterfaceWidth = 500;
    protected $btInterfaceHeight = 300;
    protected $btTable = 'btBreadcrumbs';
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btCacheBlockOutputLifetime = 300;

    /**
     * {@inheritdoc}
     */
    public function getBlockTypeName()
    {
        return t('Breadcrumbs');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockTypeDescription()
    {
        return t('Add a breadcrumbs navigation.');
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredFeatures(): array
    {
        return [
            Features::NAVIGATION,
        ];
    }

    public function add()
    {
        $this->set('includeCurrent', 1);
        $this->set('ignoreExcludeNav', 1);
    }

    /**
     * {@inheritdoc}
     */
    public function save($args)
    {
        $args['includeCurrent'] = isset($args['includeCurrent']) && $args['includeCurrent'] ? 1 : 0;
        $args['ignoreExcludeNav'] = isset($args['ignoreExcludeNav']) && $args['ignoreExcludeNav'] ? 1 : 0;
        $args['ignorePermission'] = isset($args['ignorePermission']) && $args['ignorePermission'] ? 1 : 0;
        parent::save($args);
    }

    public function view()
    {
        $page = Page::getCurrentPage();
        if ($page) {
            /** @var PageBreadcrumbFactory $factory */
            $factory = $this->app->make(PageBreadcrumbFactory::class);
            $factory->setIncludeCurrent((bool) $this->includeCurrent);
            $factory->setIgnoreExcludeNav((bool) $this->ignoreExcludeNav);

            $breadcrumb = $factory->getBreadcrumb($page);
            $this->set('breadcrumb', $breadcrumb);
        }
    }
}
