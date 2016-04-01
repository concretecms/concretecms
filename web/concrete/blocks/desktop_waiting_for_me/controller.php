<?php
namespace Concrete\Block\DesktopWaitingForMe;

defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\Block\BlockController;
use Core;

class Controller extends BlockController
{
    public $helpers = array('form');

    protected $btInterfaceWidth = 450;
    protected $btInterfaceHeight = 560;
    protected $btTable = 'btDesktopWaitingForMe';

    public function getBlockTypeDescription()
    {
        return t("Displays workflow actions waiting for you.");
    }

    public function getBlockTypeName()
    {
        return t("Waiting for Me");
    }


}
