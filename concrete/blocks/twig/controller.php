<?php

namespace Concrete\Block\Twig;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Filesystem\TwigFactory;
use Concrete\Core\Page\Page;
use Twig\Loader\ArrayLoader;

class Controller extends BlockController
{
    public $content = '';
    protected $btTable = 'btContentLocal';
    protected $btInterfaceWidth = '720';
    protected $btInterfaceHeight = '640';
    protected $btCacheBlockRecord = true;
    protected $btIgnorePageThemeGridFrameworkContainer = true;

    public function getBlockTypeDescription()
    {
        return t('Add TWIG template content.');
    }

    public function getBlockTypeName()
    {
        return t('TWIG');
    }

    public function add()
    {
        $this->edit();
    }

    public function edit()
    {
        $this->requireAsset('ace');
    }

    public function view()
    {
        $factory = $this->app->make(TwigFactory::class);
        $environment = $factory->create(new ArrayLoader(['block' => (string) $this->content]));
        $content = $environment->render('block', [
            'c' => Page::getCurrentPage(),
        ]);
        $this->set('content', $content);
    }
}
