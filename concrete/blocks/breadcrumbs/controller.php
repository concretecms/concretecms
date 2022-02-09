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
    /** @var string[]  */
    protected $helpers = ['form'];
    /** @var bool */
    public $includeCurrent;
    /** @var bool */
    public $ignoreExcludeNav;
    /** @var bool */
    public $ignorePermission;
    /** @var int  */
    protected $btInterfaceWidth = 500;
    /** @var int  */
    protected $btInterfaceHeight = 300;
    /** @var string  */
    protected $btTable = 'btBreadcrumbs';
    /** @var bool  */
    protected $btCacheBlockRecord = true;
    /** @var bool  */
    protected $btCacheBlockOutput = true;
    /** @var bool  */
    protected $btCacheBlockOutputOnPost = true;
    /** @var bool  */
    protected $btCacheBlockOutputForRegisteredUsers = true;
    /** @var int  */
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

    /**
     * @return void
     */
    public function add()
    {
        $this->set('includeCurrent', 1);
        $this->set('ignoreExcludeNav', 1);
    }

    /**
     * {@inheritdoc}
     * @return void
     */
    public function save($args)
    {
        $args['includeCurrent'] = isset($args['includeCurrent']) && $args['includeCurrent'] ? 1 : 0;
        $args['ignoreExcludeNav'] = isset($args['ignoreExcludeNav']) && $args['ignoreExcludeNav'] ? 1 : 0;
        $args['ignorePermission'] = isset($args['ignorePermission']) && $args['ignorePermission'] ? 1 : 0;
        parent::save($args);
    }

    /**
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
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
