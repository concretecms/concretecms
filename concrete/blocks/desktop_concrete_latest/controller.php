<?php
namespace Concrete\Block\DesktopConcreteLatest;

use Concrete\Core\Block\BlockController;
use Concrete\Core\ConcreteCms\ActivityService;

class Controller extends BlockController
{
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputLifetime = 7200;
    protected $btTable = 'btDesktopConcreteLatest';
    protected $btCacheBlockOutputForRegisteredUsers = true;

    public function getBlockTypeDescription()
    {
        return t("Grabs the latest information about Concrete from concretecms.com.");
    }

    public function getBlockTypeName()
    {
        return t("Desktop Latest News");
    }

    public function view()
    {
        $service = $this->app->make(ActivityService::class);
        $slots = $service->getSlotContents();
        $this->set('slot', $slots[$this->slot]);
        $this->set('key', $this->slot);
    }
}
