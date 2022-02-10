<?php

namespace Concrete\Block\DesktopConcreteLatest;

use Concrete\Core\Block\BlockController;
use Concrete\Core\ConcreteCms\ActivityService;

class Controller extends BlockController
{
    /**
     * @var bool
     */
    protected $btCacheBlockOutput = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputOnPost = true;

    /**
     * @var int
     */
    protected $btCacheBlockOutputLifetime = 7200;

    /**
     * @var string
     */
    protected $btTable = 'btDesktopConcreteLatest';

    /**
     * @var bool
     */
    protected $btCacheBlockOutputForRegisteredUsers = true;

    /**
     * @var string|null
     */
    protected $slot;

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Grabs the latest information about Concrete from concretecms.com.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Desktop Latest News');
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function view()
    {
        $service = $this->app->make(ActivityService::class);
        $slots = $service->getSlotContents();
        $this->set('slot', $slots[$this->slot] ?? null);
        $this->set('key', $this->slot);
    }
}
